<?php

namespace App\Http\Controllers\Admin;

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
        abort_if($laporan->isDraft(), 404);

        if ($request->user()->isPetugas() && $laporan->petugas_id !== $request->user()->id) {
            abort(403);
        }

        if ($laporan->is_anonymous) {
            return back()->with('gagal', 'Pelapor memilih anonim sehingga komunikasi dua arah tidak tersedia.');
        }

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
            'pengirim' => 'inspektorat',
            'isi' => $data['isi'],
            'lampiran_path' => $path,
            'lampiran_nama' => $nama,
        ]);

        $laporan->increment('unread_pelapor');

        $this->service->notifikasiPelapor(
            $laporan,
            'pesan',
            'Pesan baru dari Inspektorat',
            "{$laporan->nomor_tiket} — ada permintaan klarifikasi atau informasi tambahan.",
        );

        return back()->with('sukses', 'Pesan terkirim ke pelapor.');
    }
}
