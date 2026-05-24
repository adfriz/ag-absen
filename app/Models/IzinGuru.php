<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IzinGuru extends Model
{
    use HasFactory;

    protected $table = 'izin_guru';

    protected $fillable = [
        'user_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'jenis_izin',
        'alasan',
        'bukti_surat',
        'status',
        'disetujui_oleh',
        'catatan_admin',
    ];

    /**
     * Relasi ke Guru yang mengajukan izin
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Admin yang meninjau/menyetujui izin
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    /**
     * Relasi ke penugasan guru pengganti (substitusi) akibat izin ini
     */
    public function substitusi(): HasMany
    {
        return $this->hasMany(SubstitusiJadwal::class, 'izin_guru_id');
    }
}
