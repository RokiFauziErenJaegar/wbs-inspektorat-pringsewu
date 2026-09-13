<?php

namespace App\Http\Controllers\Pelapor;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();
        $dasar = fn () => Laporan::query()->where('user_id', $user->id);

        $perStatus = $dasar()
            ->select('status', DB::raw('count(*) as jumlah'))
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        // Enam bulan terakhir untuk grafik tren.
        $tren = collect(range(5, 0))->map(function (int $mundur) use ($user) {
            $bulan = now()->subMonths($mundur);

            return [
                'label' => $bulan->translatedFormat('M Y'),
                'jumlah' => Laporan::where('user_id', $user->id)
                    ->whereYear('created_at', $bulan->year)
                    ->whereMonth('created_at', $bulan->month)
                    ->count(),
            ];
        });

        return view('pelapor.dashboard', [
            'statistik' => [
                'total' => $perStatus->sum(),
                'draft' => $perStatus[Laporan::STATUS_DRAFT] ?? 0,
                'menunggu' => ($perStatus[Laporan::STATUS_TERKIRIM] ?? 0) + ($perStatus[Laporan::STATUS_VERIFIKASI] ?? 0),
                'diproses' => $perStatus[Laporan::STATUS_DIPROSES] ?? 0,
                'selesai' => $perStatus[Laporan::STATUS_SELESAI] ?? 0,
                'ditolak' => $perStatus[Laporan::STATUS_DITOLAK] ?? 0,
            ],
            'perStatus' => $perStatus,
            'tren' => $tren,
            'terbaru' => $dasar()->with('kategori')->latest()->limit(5)->get(),
            'perluDibaca' => $dasar()->where('unread_pelapor', '>', 0)->with('kategori')->limit(5)->get(),
        ]);
    }
}
