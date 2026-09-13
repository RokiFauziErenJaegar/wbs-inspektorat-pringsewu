<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Laporan extends Model
{
    use SoftDeletes;

    protected $table = 'laporan';

    public const STATUS_DRAFT = 'draft';

    public const STATUS_TERKIRIM = 'terkirim';

    public const STATUS_VERIFIKASI = 'verifikasi';

    public const STATUS_DIPROSES = 'diproses';

    public const STATUS_SELESAI = 'selesai';

    public const STATUS_DITOLAK = 'ditolak';

    /** Label ramah pengguna untuk tiap status. */
    public const STATUS = [
        self::STATUS_DRAFT => 'Draf',
        self::STATUS_TERKIRIM => 'Menunggu Verifikasi',
        self::STATUS_VERIFIKASI => 'Sedang Diverifikasi',
        self::STATUS_DIPROSES => 'Dalam Tindak Lanjut',
        self::STATUS_SELESAI => 'Selesai',
        self::STATUS_DITOLAK => 'Ditolak',
    ];

    public const PRIORITAS = [
        'rendah' => 'Rendah',
        'sedang' => 'Sedang',
        'tinggi' => 'Tinggi',
        'mendesak' => 'Mendesak',
    ];

    protected $fillable = [
        'nomor_tiket', 'kode_akses', 'user_id', 'kategori_id', 'opd_id',
        'judul', 'uraian', 'lokasi_kejadian', 'tanggal_kejadian', 'nilai_kerugian',
        'is_anonymous', 'status', 'prioritas', 'petugas_id', 'verified_by',
        'submitted_at', 'verified_at', 'selesai_at', 'deadline',
        'alasan_penolakan', 'kesimpulan', 'rekomendasi',
        'unread_pelapor', 'unread_admin',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kejadian' => 'date',
            'deadline' => 'date',
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
            'selesai_at' => 'datetime',
            'is_anonymous' => 'boolean',
            'nilai_kerugian' => 'decimal:2',
        ];
    }

    // ------------------------------------------------------------------
    // Relasi
    // ------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class);
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id')->withTrashed();
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by')->withTrashed();
    }

    public function terlapor(): HasMany
    {
        return $this->hasMany(Terlapor::class);
    }

    public function lampiran(): HasMany
    {
        return $this->hasMany(Lampiran::class);
    }

    public function tindakLanjut(): HasMany
    {
        return $this->hasMany(TindakLanjut::class)->latest();
    }

    public function pesan(): HasMany
    {
        return $this->hasMany(Pesan::class)->oldest();
    }

    // ------------------------------------------------------------------
    // Scope
    // ------------------------------------------------------------------

    public function scopeMilik(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    /** Laporan yang sudah benar-benar dikirim (bukan draf). */
    public function scopeTerkirim(Builder $query): Builder
    {
        return $query->where('status', '!=', self::STATUS_DRAFT);
    }

    public function scopeCari(Builder $query, ?string $kata): Builder
    {
        if (blank($kata)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($kata) {
            $q->where('judul', 'like', "%{$kata}%")
                ->orWhere('nomor_tiket', 'like', "%{$kata}%")
                ->orWhere('uraian', 'like', "%{$kata}%");
        });
    }

    // ------------------------------------------------------------------
    // Helper
    // ------------------------------------------------------------------

    public static function buatNomorTiket(): string
    {
        $prefix = 'WBS-'.now()->format('Ymd');

        $terakhir = static::withTrashed()
            ->where('nomor_tiket', 'like', $prefix.'%')
            ->orderByDesc('nomor_tiket')
            ->value('nomor_tiket');

        $urutan = $terakhir ? ((int) Str::afterLast($terakhir, '-')) + 1 : 1;

        return $prefix.'-'.str_pad((string) $urutan, 4, '0', STR_PAD_LEFT);
    }

    public static function buatKodeAkses(): string
    {
        return Str::upper(Str::random(8));
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isSelesai(): bool
    {
        return in_array($this->status, [self::STATUS_SELESAI, self::STATUS_DITOLAK], true);
    }

    public function bisaDiedit(): bool
    {
        return $this->isDraft();
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getPrioritasLabelAttribute(): string
    {
        return self::PRIORITAS[$this->prioritas] ?? $this->prioritas;
    }

    /** Kelas Tailwind untuk lencana status. */
    public function getStatusKelasAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'bg-slate-100 text-slate-700 ring-slate-600/20 dark:bg-slate-500/15 dark:text-slate-300 dark:ring-slate-400/30',
            self::STATUS_TERKIRIM => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/15 dark:text-amber-300 dark:ring-amber-400/30',
            self::STATUS_VERIFIKASI => 'bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-500/15 dark:text-sky-300 dark:ring-sky-400/30',
            self::STATUS_DIPROSES => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20 dark:bg-indigo-500/15 dark:text-indigo-300 dark:ring-indigo-400/30',
            self::STATUS_SELESAI => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-400/30',
            self::STATUS_DITOLAK => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/15 dark:text-rose-300 dark:ring-rose-400/30',
            default => 'bg-slate-100 text-slate-700 ring-slate-600/20',
        };
    }

    public function getPrioritasKelasAttribute(): string
    {
        return match ($this->prioritas) {
            'rendah' => 'bg-slate-100 text-slate-600 dark:bg-slate-500/15 dark:text-slate-300',
            'sedang' => 'bg-sky-100 text-sky-700 dark:bg-sky-500/15 dark:text-sky-300',
            'tinggi' => 'bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300',
            'mendesak' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300',
            default => 'bg-slate-100 text-slate-600',
        };
    }

    /** Persentase kemajuan untuk progress bar. */
    public function getProgresAttribute(): int
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 5,
            self::STATUS_TERKIRIM => 25,
            self::STATUS_VERIFIKASI => 50,
            self::STATUS_DIPROSES => 75,
            self::STATUS_SELESAI, self::STATUS_DITOLAK => 100,
            default => 0,
        };
    }

    public function getNamaPelaporAttribute(): string
    {
        if ($this->is_anonymous) {
            return 'Pelapor Anonim';
        }

        return $this->user?->name ?? 'Tidak diketahui';
    }
}
