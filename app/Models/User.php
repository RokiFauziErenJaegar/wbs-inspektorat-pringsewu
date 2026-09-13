<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_PETUGAS = 'petugas';

    public const ROLE_PELAPOR = 'pelapor';

    protected $fillable = [
        'username',
        'name',
        'opd_id',
        'nip',
        'jabatan',
        'telepon',
        'alamat',
        'email',
        'password',
        'role',
        'avatar',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = ['password', 'remember_token'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class);
    }

    public function laporan(): HasMany
    {
        return $this->hasMany(Laporan::class);
    }

    public function laporanDitangani(): HasMany
    {
        return $this->hasMany(Laporan::class, 'petugas_id');
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class)->latest();
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isPetugas(): bool
    {
        return $this->role === self::ROLE_PETUGAS;
    }

    public function isPelapor(): bool
    {
        return $this->role === self::ROLE_PELAPOR;
    }

    /** Admin maupun petugas sama-sama bekerja di lingkungan Inspektorat. */
    public function isInspektorat(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_PETUGAS], true);
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'Administrator Inspektorat',
            self::ROLE_PETUGAS => 'Petugas Inspektorat',
            default => 'Pelapor OPD',
        };
    }

    /**
     * Nama panggilan untuk sapaan: melewati gelar depan seperti
     * "dr.", "Ir.", "Hj.", dan membuang gelar belakang setelah koma.
     */
    public function getPanggilanAttribute(): string
    {
        $kata = $this->kataNama();

        return $kata[0] ?? ($this->username ?? 'Pengguna');
    }

    public function getInisialAttribute(): string
    {
        $kata = array_slice($this->kataNama(), 0, 2);

        return $kata === []
            ? Str::upper(Str::substr($this->username ?? 'U', 0, 2))
            : Str::upper(implode('', array_map(fn ($p) => Str::substr($p, 0, 1), $kata)));
    }

    /**
     * Pecah nama menjadi kata tanpa gelar depan dan gelar belakang.
     *
     * @return list<string>
     */
    private function kataNama(): array
    {
        $gelar = ['dr', 'drg', 'ir', 'drs', 'dra', 'h', 'hj', 'prof', 'kh', 'r', 'rr'];

        $nama = trim(Str::before($this->name ?? '', ','));
        $kata = array_filter(preg_split('/\s+/', $nama) ?: []);

        $kata = array_filter(
            $kata,
            fn ($bagian) => ! in_array(Str::lower(rtrim($bagian, '.')), $gelar, true),
        );

        return array_values($kata);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar ? asset('storage/'.$this->avatar) : null;
    }

    public function notifikasiBelumDibaca(): int
    {
        return $this->notifikasi()->whereNull('dibaca_at')->count();
    }
}
