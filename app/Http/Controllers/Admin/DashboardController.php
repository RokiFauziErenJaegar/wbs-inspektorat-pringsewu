<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\Opd;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();

        // Petugas hanya melihat berkas yang didisposisikan kepadanya.
        $lingkup = fn () => Laporan::query()
            ->terkirim()
            ->when($user->isPetugas(), fn ($q) => $q->where('petugas_id', $user->id));

        $perStatus = (clone $lingkup())
            ->select('status', DB::raw('count(*) as jumlah'))
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $tren = collect(range(11, 0))->map(function (int $mundur) use ($lingkup) {
            $bulan = now()->subMonths($mundur);

            return [
                'label' => $bulan->translatedFormat('M y'),
                'masuk' => (clone $lingkup())
                    ->whereYear('submitted_at', $bulan->year)
                    ->whereMonth('submitted_at', $bulan->month)
                    ->count(),
                'selesai' => (clone $lingkup())
                    ->whereYear('selesai_at', $bulan->year)
                    ->whereMonth('selesai_at', $bulan->month)
                    ->count(),
            ];
        });

        $perKategori = Kategori::query()
            ->withCount(['laporan' => fn ($q) => $q->terkirim()
                ->when($user->isPetugas(), fn ($qq) => $qq->where('petugas_id', $user->id))])
            ->orderByDesc('laporan_count')
            ->get();

        $perOpd = Opd::query()
            ->withCount(['laporan' => fn ($q) => $q->terkirim()])
            ->having('laporan_count', '>', 0)
            ->orderByDesc('laporan_count')
            ->limit(6)
            ->get();

        $total = $perStatus->sum();
        $selesai = $perStatus[Laporan::STATUS_SELESAI] ?? 0;

        return view('admin.dashboard', [
            'statistik' => [
                'total' => $total,
                'baru' => $perStatus[Laporan::STATUS_TERKIRIM] ?? 0,
                'verifikasi' => $perStatus[Laporan::STATUS_VERIFIKASI] ?? 0,
                'diproses' => $perStatus[Laporan::STATUS_DIPROSES] ?? 0,
                'selesai' => $selesai,
                'ditolak' => $perStatus[Laporan::STATUS_DITOLAK] ?? 0,
                'penyelesaian' => $total > 0 ? round($selesai / $total * 100) : 0,
                'kerugian' => (clone $lingkup())->sum('nilai_kerugian'),
                'pelapor' => User::where('role', User::ROLE_PELAPOR)->count(),
                'opd' => Opd::aktif()->count(),
            ],
            'perStatus' => $perStatus,
            'tren' => $tren,
            'perKategori' => $perKategori,
            'perOpd' => $perOpd,
            'antrian' => (clone $lingkup())
                ->whereIn('status', [Laporan::STATUS_TERKIRIM, Laporan::STATUS_VERIFIKASI])
                ->with(['kategori', 'user', 'opd'])
                ->oldest('submitted_at')
                ->limit(6)
                ->get(),
            'prioritas' => (clone $lingkup())
                ->whereIn('prioritas', ['tinggi', 'mendesak'])
                ->whereNotIn('status', [Laporan::STATUS_SELESAI, Laporan::STATUS_DITOLAK])
                ->with(['kategori', 'petugas'])
                ->latest('submitted_at')
                ->limit(5)
                ->get(),
            'jatuhTempo' => (clone $lingkup())
                ->whereNotNull('deadline')
                ->whereDate('deadline', '<=', now()->addDays(7))
                ->whereNotIn('status', [Laporan::STATUS_SELESAI, Laporan::STATUS_DITOLAK])
                ->with('petugas')
                ->orderBy('deadline')
                ->limit(5)
                ->get(),
            'kinerjaPetugas' => $user->isAdmin()
                ? User::where('role', User::ROLE_PETUGAS)
                    ->withCount([
                        'laporanDitangani',
                        'laporanDitangani as selesai_count' => fn ($q) => $q->where('status', Laporan::STATUS_SELESAI),
                    ])
                    ->orderByDesc('laporan_ditangani_count')
                    ->limit(5)
                    ->get()
                : collect(),
        ]);
    }
}
