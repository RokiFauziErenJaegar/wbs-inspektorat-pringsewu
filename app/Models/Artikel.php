<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Artikel extends Model
{
    protected $table = 'artikel';

    protected $fillable = [
        'judul', 'slug', 'ringkasan', 'konten', 'gambar',
        'sumber', 'user_id', 'is_published', 'published_at', 'dilihat',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Artikel $artikel) {
            if (blank($artikel->slug)) {
                $dasar = Str::slug($artikel->judul);
                $slug = $dasar;
                $i = 2;
                while (static::where('slug', $slug)->whereKeyNot($artikel->id ?? 0)->exists()) {
                    $slug = $dasar.'-'.$i++;
                }
                $artikel->slug = $slug;
            }

            if (blank($artikel->ringkasan)) {
                $artikel->ringkasan = Str::limit(strip_tags($artikel->konten), 180);
            }
        });
    }

    public function penulis(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function scopeTerbit($query)
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getGambarUrlAttribute(): ?string
    {
        return $this->gambar ? asset('storage/'.$this->gambar) : null;
    }
}
