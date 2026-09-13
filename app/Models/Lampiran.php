<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Lampiran extends Model
{
    protected $table = 'lampiran';

    protected $fillable = [
        'laporan_id', 'uploaded_by', 'nama_file', 'path',
        'mime_type', 'ukuran', 'keterangan', 'is_internal',
    ];

    protected function casts(): array
    {
        return ['is_internal' => 'boolean', 'ukuran' => 'integer'];
    }

    protected static function booted(): void
    {
        static::deleting(function (Lampiran $lampiran) {
            Storage::disk('public')->delete($lampiran->path);
        });
    }

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Laporan::class);
    }

    public function pengunggah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by')->withTrashed();
    }

    public function getEkstensiAttribute(): string
    {
        return Str::lower(pathinfo($this->nama_file, PATHINFO_EXTENSION));
    }

    public function getUkuranTerbacaAttribute(): string
    {
        $bytes = (int) $this->ukuran;

        foreach (['B', 'KB', 'MB', 'GB'] as $satuan) {
            if ($bytes < 1024) {
                return round($bytes, $satuan === 'B' ? 0 : 1).' '.$satuan;
            }
            $bytes /= 1024;
        }

        return round($bytes, 1).' TB';
    }

    public function getIsGambarAttribute(): bool
    {
        return Str::startsWith((string) $this->mime_type, 'image/');
    }
}
