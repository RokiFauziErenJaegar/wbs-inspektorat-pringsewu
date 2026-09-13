<?php

namespace App\Services;

use App\Models\Laporan;
use App\Models\Notifikasi;
use App\Models\TindakLanjut;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Menyatukan efek samping dari setiap perubahan status laporan:
 * pencatatan garis waktu dan pengiriman notifikasi.
 */
class LaporanService
{
    public function catat(
        Laporan $laporan,
        string $tipe,
        string $judul,
        ?string $catatan = null,
        ?string $statusLama = null,
        ?string $statusBaru = null,
        bool $internal = false,
        ?User $oleh = null,
    ): TindakLanjut {
        return $laporan->tindakLanjut()->create([
            'user_id' => $oleh?->id ?? auth()->id(),
            'tipe' => $tipe,
            'judul' => $judul,
            'catatan' => $catatan,
            'status_lama' => $statusLama,
            'status_baru' => $statusBaru,
            'is_internal' => $internal,
        ]);
    }

    public function notifikasiPelapor(Laporan $laporan, string $tipe, string $judul, ?string $pesan = null): void
    {
        Notifikasi::create([
            'user_id' => $laporan->user_id,
            'laporan_id' => $laporan->id,
            'tipe' => $tipe,
            'judul' => $judul,
            'pesan' => $pesan,
            'url' => route('pelapor.laporan.show', $laporan),
        ]);
    }

    /** Kirim notifikasi ke seluruh admin, opsional ditambah petugas tertentu. */
    public function notifikasiInspektorat(
        Laporan $laporan,
        string $tipe,
        string $judul,
        ?string $pesan = null,
        ?User $petugas = null,
    ): void {
        $penerima = User::query()
            ->where('is_active', true)
            ->where('role', User::ROLE_ADMIN)
            ->pluck('id');

        if ($petugas) {
            $penerima = $penerima->push($petugas->id);
        }

        $this->kirimMassal($penerima->unique(), $laporan, $tipe, $judul, $pesan);
    }

    private function kirimMassal(Collection $userIds, Laporan $laporan, string $tipe, string $judul, ?string $pesan): void
    {
        $now = now();

        $baris = $userIds->map(fn ($id) => [
            'user_id' => $id,
            'laporan_id' => $laporan->id,
            'tipe' => $tipe,
            'judul' => $judul,
            'pesan' => $pesan,
            'url' => route('admin.laporan.show', $laporan),
            'created_at' => $now,
            'updated_at' => $now,
        ])->all();

        if ($baris !== []) {
            Notifikasi::insert($baris);
        }
    }
}
