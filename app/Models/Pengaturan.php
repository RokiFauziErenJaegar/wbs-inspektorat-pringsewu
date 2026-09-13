<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $primaryKey = 'kunci';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['kunci', 'nilai', 'kelompok'];

    public static function ambil(string $kunci, mixed $bawaan = null): mixed
    {
        return Cache::rememberForever('pengaturan', function () {
            return static::pluck('nilai', 'kunci')->all();
        })[$kunci] ?? $bawaan;
    }

    public static function simpan(string $kunci, mixed $nilai, string $kelompok = 'umum'): void
    {
        static::updateOrCreate(['kunci' => $kunci], ['nilai' => $nilai, 'kelompok' => $kelompok]);
        Cache::forget('pengaturan');
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('pengaturan'));
        static::deleted(fn () => Cache::forget('pengaturan'));
    }
}
