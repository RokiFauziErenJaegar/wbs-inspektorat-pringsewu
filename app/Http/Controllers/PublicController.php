<?php

namespace App\Http\Controllers;

use App\Models\Artikel;
use App\Models\Faq;
use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\Opd;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function beranda(): View
    {
        return view('publik.beranda', [
            'kategori' => Kategori::aktif()->urut()->get(),
            'artikel' => Artikel::terbit()->latest('published_at')->limit(3)->get(),
            'faq' => Faq::aktif()->urut()->limit(5)->get(),
            'statistik' => [
                'laporan' => Laporan::terkirim()->count(),
                'selesai' => Laporan::where('status', Laporan::STATUS_SELESAI)->count(),
                'diproses' => Laporan::whereIn('status', [
                    Laporan::STATUS_TERKIRIM,
                    Laporan::STATUS_VERIFIKASI,
                    Laporan::STATUS_DIPROSES,
                ])->count(),
                'opd' => Opd::aktif()->count(),
            ],
        ]);
    }

    public function caraMelapor(): View
    {
        return view('publik.cara-melapor', [
            'kategori' => Kategori::aktif()->urut()->get(),
        ]);
    }

    public function berita(Request $request): View
    {
        return view('publik.berita', [
            'artikel' => Artikel::terbit()
                ->when($request->string('q')->toString(), fn ($q, $kata) => $q->where('judul', 'like', "%{$kata}%"))
                ->latest('published_at')
                ->paginate(9)
                ->withQueryString(),
        ]);
    }

    public function beritaDetail(Artikel $artikel): View
    {
        abort_unless($artikel->is_published, 404);

        $artikel->increment('dilihat');

        return view('publik.berita-detail', [
            'artikel' => $artikel,
            'lainnya' => Artikel::terbit()->whereKeyNot($artikel->id)->latest('published_at')->limit(4)->get(),
        ]);
    }

    public function faq(): View
    {
        return view('publik.faq', [
            'kelompok' => Faq::aktif()->urut()->get()->groupBy('kelompok'),
        ]);
    }

    public function lacak(): View
    {
        return view('publik.lacak', ['laporan' => null]);
    }

    public function lacakCari(Request $request): View
    {
        $data = $request->validate([
            'nomor_tiket' => ['required', 'string', 'max:30'],
            'kode_akses' => ['required', 'string', 'max:16'],
        ], [], [
            'nomor_tiket' => 'nomor tiket',
            'kode_akses' => 'kode akses',
        ]);

        $laporan = Laporan::with(['kategori', 'tindakLanjut' => fn ($q) => $q->where('is_internal', false)])
            ->where('nomor_tiket', trim($data['nomor_tiket']))
            ->where('kode_akses', strtoupper(trim($data['kode_akses'])))
            ->terkirim()
            ->first();

        return view('publik.lacak', [
            'laporan' => $laporan,
            'dicari' => true,
        ]);
    }
}
