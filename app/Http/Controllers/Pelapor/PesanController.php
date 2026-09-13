<?php

namespace App\Http\Controllers\Pelapor;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Services\LaporanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PesanController extends Controller
{
    public function __construct(private readonly LaporanService $service) {}

    public function store(Request $request, Laporan $laporan): RedirectResponse
    {
        abort_unless($laporan->user_id === $request->user()->id, 403);
        abort_if($laporan->isDraft(), 403, 'Kirim pengaduan terlebih dahulu untuk memulai komunikasi.');

        $data = $request->validate([
            'isi' => ['required', 'string', 'max:5000'],
            'lampiran' => ['nullable', 'file', 'extensions:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,zip,rar', 'max:20480'],
        ], [], ['isi' => 'pesan']);

        $path = null;
        $nama = null;

        if ($berkas = $request->file('lampiran')) {
            $path = $berkas->store("pesan/{$laporan->id}", 'public');
            $nama = $berkas->getClientOriginalName();
        }

        $laporan->pesan()->create([
            'user_id' => $request->user()->id,
            'pengirim' => 'pelapor',
            'isi' => $data['isi'],
            'lampiran_path' => $path,
            'lampiran_nama' => $nama,
        ]);

        $laporan->increment('unread_admin');

        $this->service->notifikasiInspektorat(
            $laporan,
            'pesan',
            'Pesan baru dari pelapor',
            "{$laporan->nomor_tiket} — {$laporan->judul}",
            $laporan->petugas,
        );

        return back()->with('sukses', 'Pesan terkirim ke Inspektorat.');
    }
}
