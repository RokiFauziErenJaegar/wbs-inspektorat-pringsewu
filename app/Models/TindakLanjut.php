<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TindakLanjut extends Model
{
    protected $table = 'tindak_lanjut';

    protected $fillable = [
        'laporan_id', 'user_id', 'tipe', 'judul', 'catatan',
        'status_lama', 'status_baru', 'is_internal',
    ];

    protected function casts(): array
    {
        return ['is_internal' => 'boolean'];
    }

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Laporan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /** Ikon dan warna garis waktu per jenis kejadian. */
    public function getTampilanAttribute(): array
    {
        return match ($this->tipe) {
            'dibuat' => ['icon' => 'pencil', 'warna' => 'slate'],
            'dikirim' => ['icon' => 'paper-plane', 'warna' => 'amber'],
            'verifikasi' => ['icon' => 'shield-check', 'warna' => 'sky'],
            'ditolak' => ['icon' => 'x-circle', 'warna' => 'rose'],
            'disposisi' => ['icon' => 'users', 'warna' => 'violet'],
            'progres' => ['icon' => 'refresh', 'warna' => 'indigo'],
            'selesai' => ['icon' => 'check-badge', 'warna' => 'emerald'],
            'lampiran' => ['icon' => 'paper-clip', 'warna' => 'cyan'],
            default => ['icon' => 'chat', 'warna' => 'slate'],
        };
    }
}
