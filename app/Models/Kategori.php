<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Kategori extends Model
{
    protected $table = 'kategori';

    protected $fillable = [
        'nama', 'slug', 'kode', 'deskripsi', 'petunjuk',
        'icon', 'warna', 'urutan', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::saving(function (Kategori $kategori) {
            if (blank($kategori->slug)) {
                $dasar = Str::slug($kategori->nama);
                $slug = $dasar;
                $i = 2;
                while (static::where('slug', $slug)->whereKeyNot($kategori->id ?? 0)->exists()) {
                    $slug = $dasar.'-'.$i++;
                }
                $kategori->slug = $slug;
            }
        });
    }

    public function laporan(): HasMany
    {
        return $this->hasMany(Laporan::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeUrut($query)
    {
        return $query->orderBy('urutan')->orderBy('nama');
    }
}
