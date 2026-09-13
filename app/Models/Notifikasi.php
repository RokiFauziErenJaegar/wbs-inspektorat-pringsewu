<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    protected $fillable = [
        'user_id', 'laporan_id', 'tipe', 'judul', 'pesan', 'url', 'dibaca_at',
    ];

    protected function casts(): array
    {
        return ['dibaca_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Laporan::class);
    }

    public function scopeBelumDibaca($query)
    {
        return $query->whereNull('dibaca_at');
    }

    public function getIkonAttribute(): string
    {
        return match ($this->tipe) {
            'laporan_baru' => 'inbox',
            'verifikasi' => 'shield-check',
            'ditolak' => 'x-circle',
            'disposisi' => 'users',
            'progres' => 'refresh',
            'selesai' => 'check-badge',
            'pesan' => 'chat',
            default => 'bell',
        };
    }
}
