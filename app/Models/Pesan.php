<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Pesan extends Model
{
    protected $table = 'pesan';

    protected $fillable = [
        'laporan_id', 'user_id', 'pengirim', 'isi',
        'lampiran_path', 'lampiran_nama', 'dibaca_at',
    ];

    protected function casts(): array
    {
        return ['dibaca_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::deleting(function (Pesan $pesan) {
            if ($pesan->lampiran_path) {
                Storage::disk('public')->delete($pesan->lampiran_path);
            }
        });
    }

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Laporan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function dariInspektorat(): bool
    {
        return $this->pengirim === 'inspektorat';
    }
}
