<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_WARGA = 'mahasiswa';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'foto',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* -----------------------------------------------------------------
     |  Helper role (dipakai di Blade, controller, dan middleware)
     | ----------------------------------------------------------------- */

    public function isPetugas(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isMahasiswa(): bool
    {
        return $this->role === self::ROLE_WARGA;
    }

    /**
     * Hanya petugas yang boleh mengelola data fasilitas & pengaduan.
     */
    public function canManage(): bool
    {
        return $this->isPetugas();
    }

   

    public function getRoleLabelAttribute(): string
    {
        return ucfirst($this->role);
    }

    /* -----------------------------------------------------------------
     |  Relasi : satu mahasiswa dapat membuat banyak pengaduan
     | ----------------------------------------------------------------- */

    public function pengaduan(): HasMany
    {
        return $this->hasMany(\Modules\Laporan\Models\Laporan::class);
    }
}
