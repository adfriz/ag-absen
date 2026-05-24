<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubstitusiJadwal extends Model
{
    use HasFactory;

    protected $table = 'substitusi_jadwal';

    protected $fillable = [
        'izin_guru_id',
        'jadwal_id',
        'tanggal',
        'guru_pengganti_id',
    ];

    /**
     * Relasi ke pengajuan izin guru terkait
     */
    public function izinGuru(): BelongsTo
    {
        return $this->belongsTo(IzinGuru::class, 'izin_guru_id');
    }

    /**
     * Relasi ke Jadwal asli pelajaran
     */
    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }

    /**
     * Relasi ke Guru Pengganti
     */
    public function substituteTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_pengganti_id');
    }
}
