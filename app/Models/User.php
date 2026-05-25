<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Menentukan siapa yang boleh masuk ke panel
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'app') {
            return $this->role === 'guru' || $this->role === 'admin';
        }

        return false;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'needs_password_reset',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relasi ke Jadwal mengajar milik guru ini
     */
    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'user_id');
    }

    /**
     * Relasi ke Pengajuan Izin milik guru ini
     */
    public function izinGuru()
    {
        return $this->hasMany(IzinGuru::class, 'user_id');
    }

    /**
     * Relasi ke Penugasan Guru Pengganti (substitusi) milik guru ini
     */
    public function substitusiJadwal()
    {
        return $this->hasMany(SubstitusiJadwal::class, 'guru_pengganti_id');
    }
}
