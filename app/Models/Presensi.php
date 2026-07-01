<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';

    protected $fillable = [
        'siswa_id',
        'jadwal_id',
        'tanggal',
        'status',
        'catatan',
        'menit_terlambat',
        'bukti_surat',
        'diabsen_oleh',
    ];

    protected static function booted(): void
    {
        static::updating(function (self $presensi) {
            if ($presensi->isDirty('bukti_surat') && $presensi->getOriginal('bukti_surat')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($presensi->getOriginal('bukti_surat'));
            }
        });

        static::deleting(function (self $presensi) {
            if ($presensi->bukti_surat) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($presensi->bukti_surat);
            }
        });
    }

    /**
     * Relasi ke Siswa
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Relasi ke Jadwal Pelajaran
     */
    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }

    /**
     * Relasi ke Guru yang melakukan absensi (diabsen_oleh)
     */
    public function guruAbsen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diabsen_oleh');
    }
}
