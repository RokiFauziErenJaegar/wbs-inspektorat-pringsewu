<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Opd extends Model
{
    protected $table = 'opd';

    protected $fillable = [
        'nama', 'singkatan', 'slug', 'jenis', 'alamat',
        'telepon', 'email', 'kepala', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::saving(function (Opd $opd) {
            if (blank($opd->slug)) {
                $opd->slug = static::slugUnik($opd->nama, $opd->id);
            }
        });
    }

    public static function slugUnik(string $nama, ?int $abaikanId = null): string
    {
        $dasar = Str::slug($nama);
        $slug = $dasar;
        $i = 2;

        while (static::where('slug', $slug)->when($abaikanId, fn ($q) => $q->whereKeyNot($abaikanId))->exists()) {
            $slug = $dasar.'-'.$i++;
        }

        return $slug;
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function laporan(): HasMany
    {
        return $this->hasMany(Laporan::class);
    }

    public function getNamaSingkatAttribute(): string
    {
        return $this->singkatan ?: $this->nama;
    }

    public function getJenisLabelAttribute(): string
    {
        return match ($this->jenis) {
            'sekretariat' => 'Sekretariat',
            'dinas' => 'Dinas',
            'badan' => 'Badan',
            'inspektorat' => 'Inspektorat',
            'kecamatan' => 'Kecamatan',
            'rsud' => 'RSUD',
            'satuan' => 'Satuan',
            default => 'Lainnya',
        };
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }
}
