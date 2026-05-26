<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'nama',
        'tingkat',
    ];

    /**
     * Relasi ke siswa yang terdaftar di kelas ini lintas tahun ajaran
     */
    public function siswa(): BelongsToMany
    {
        return $this->belongsToMany(Siswa::class, 'kelas_siswa', 'kelas_id', 'siswa_id')
            ->withPivot(['tahun_ajaran_id', 'nomor_absen'])
            ->withTimestamps();
    }

    /**
     * Relasi ke Jadwal pelajaran di kelas ini
     */
    public function jadwal(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'kelas_id');
    }
}
