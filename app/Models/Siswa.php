<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    protected $fillable = [
        'nisn',
        'nama',
        'jenis_kelamin',
    ];

    /**
     * Relasi ke Kelas tempat siswa terdaftar lintas tahun ajaran
     */
    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(Kelas::class, 'kelas_siswa', 'siswa_id', 'kelas_id')
            ->withPivot('tahun_ajaran_id', 'nomor_absen')
            ->withTimestamps();
    }

    /**
     * Relasi ke Catatan Kehadiran (Presensi) siswa
     */
    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class, 'siswa_id');
    }

    /**
     * Helper untuk mendapatkan Kelas siswa pada tahun ajaran yang sedang aktif saat ini
     */
    public function kelasAktif()
    {
        $tahunAktif = TahunAjaran::where('apakah_aktif', true)->first();
        if (!$tahunAktif) {
            return null;
        }
        return $this->kelas()->wherePivot('tahun_ajaran_id', $tahunAktif->id)->first();
    }
}
