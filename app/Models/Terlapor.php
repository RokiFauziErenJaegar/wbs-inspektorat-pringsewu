<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Terlapor extends Model
{
    protected $table = 'terlapor';

    public const KLASIFIKASI = [
        'pejabat_struktural' => 'Pejabat Struktural',
        'pejabat_fungsional' => 'Pejabat Fungsional',
        'pelaksana' => 'Pelaksana / Staf',
        'pppk' => 'PPPK',
        'honorer' => 'Tenaga Honorer',
        'pihak_ketiga' => 'Pihak Ketiga / Rekanan',
        'lainnya' => 'Lainnya',
    ];

    protected $fillable = [
        'laporan_id', 'nama', 'jabatan', 'instansi', 'klasifikasi', 'keterangan',
    ];

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Laporan::class);
    }

    public function getKlasifikasiLabelAttribute(): string
    {
        return self::KLASIFIKASI[$this->klasifikasi] ?? 'Lainnya';
    }
}
