<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\Opd;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StatistikController extends Controller
{
    public function index(Request $request): View
    {
        $tahun = (int) $request->input('tahun', now()->year);

        $dasar = fn () => Laporan::terkirim()->whereYear('submitted_at', $tahun);

        $perBulan = collect(range(1, 12))->map(function (int $bulan) use ($tahun) {
            $masuk = Laporan::terkirim()->whereYear('submitted_at', $tahun)->whereMonth('submitted_at', $bulan);

            return [
                'label' => Carbon::create($tahun, $bulan, 1)->translatedFormat('M'),
                // Berkas yang masuk pada bulan tersebut.
                'masuk' => (clone $masuk)->count(),
                // Berkas yang dituntaskan pada bulan tersebut, dari angkatan mana pun.
                'selesai' => Laporan::whereYear('selesai_at', $tahun)->whereMonth('selesai_at', $bulan)
                    ->where('status', Laporan::STATUS_SELESAI)->count(),
                // Dari berkas yang masuk bulan tersebut, berapa yang kini sudah selesai.
                'tuntas' => (clone $masuk)->where('status', Laporan::STATUS_SELESAI)->count(),
            ];
        });

        // Rata-rata hari penyelesaian pada tahun berjalan.
        $rataHari = Laporan::whereYear('selesai_at', $tahun)
            ->whereNotNull('submitted_at')
            ->where('status', Laporan::STATUS_SELESAI)
            ->select(DB::raw('AVG(DATEDIFF(selesai_at, submitted_at)) as rata'))
            ->value('rata');

        return view('admin.statistik', [
            'tahun' => $tahun,
            'tahunTersedia' => Laporan::terkirim()
                ->select(DB::raw('DISTINCT YEAR(submitted_at) as tahun'))
                ->orderByDesc('tahun')
                ->pluck('tahun')
                ->prepend(now()->year)
                ->unique()
                ->values(),
            'perBulan' => $perBulan,
            'perStatus' => (clone $dasar())->select('status', DB::raw('count(*) as jumlah'))->groupBy('status')->pluck('jumlah', 'status'),
            'perPrioritas' => (clone $dasar())->select('prioritas', DB::raw('count(*) as jumlah'))->groupBy('prioritas')->pluck('jumlah', 'prioritas'),
            'perKategori' => Kategori::withCount(['laporan' => fn ($q) => $q->terkirim()->whereYear('submitted_at', $tahun)])
                ->orderByDesc('laporan_count')->get(),
            'perOpd' => Opd::withCount(['laporan' => fn ($q) => $q->terkirim()->whereYear('submitted_at', $tahun)])
                ->having('laporan_count', '>', 0)->orderByDesc('laporan_count')->limit(10)->get(),
            'ringkasan' => [
                'total' => (clone $dasar())->count(),
                'kerugian' => (clone $dasar())->sum('nilai_kerugian'),
                'rataHari' => $rataHari ? round((float) $rataHari, 1) : null,
                'anonim' => (clone $dasar())->where('is_anonymous', true)->count(),
            ],
            'kinerjaPetugas' => User::where('role', User::ROLE_PETUGAS)
                ->withCount([
                    'laporanDitangani as total' => fn ($q) => $q->terkirim()->whereYear('submitted_at', $tahun),
                    'laporanDitangani as selesai' => fn ($q) => $q->where('status', Laporan::STATUS_SELESAI)->whereYear('submitted_at', $tahun),
                ])
                ->orderByDesc('total')
                ->get(),
        ]);
    }

    /** Unduh rekapitulasi pengaduan dalam format CSV (dapat dibuka di Excel). */
    public function ekspor(Request $request): StreamedResponse
    {
        $laporan = Laporan::terkirim()
            ->with(['kategori', 'user.opd', 'opd', 'petugas'])
            ->when($request->string('status')->toString(), fn ($q, $s) => $q->where('status', $s))
            ->when($request->input('tahun'), fn ($q, $t) => $q->whereYear('submitted_at', $t))
            ->when($request->user()->isPetugas(), fn ($q) => $q->where('petugas_id', $request->user()->id))
            ->latest('submitted_at')
            ->get();

        $namaBerkas = 'rekap-pengaduan-wbs-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($laporan) {
            $keluaran = fopen('php://output', 'w');

            // BOM agar Excel membaca UTF-8 dengan benar.
            fwrite($keluaran, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($keluaran, [
                'Nomor Tiket', 'Tanggal Kirim', 'Judul', 'Kategori', 'OPD Pelapor',
                'OPD Terlapor', 'Pelapor', 'Status', 'Prioritas', 'Petugas',
                'Nilai Kerugian', 'Tanggal Selesai',
            ], ';');

            foreach ($laporan as $baris) {
                fputcsv($keluaran, [
                    $baris->nomor_tiket,
                    $baris->submitted_at?->format('d/m/Y H:i'),
                    $baris->judul,
                    $baris->kategori?->nama,
                    $baris->user?->opd?->nama,
                    $baris->opd?->nama,
                    $baris->nama_pelapor,
                    $baris->status_label,
                    $baris->prioritas_label,
                    $baris->petugas?->name ?? '-',
                    $baris->nilai_kerugian ? number_format((float) $baris->nilai_kerugian, 0, ',', '.') : '',
                    $baris->selesai_at?->format('d/m/Y'),
                ], ';');
            }

            fclose($keluaran);
        }, $namaBerkas, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
