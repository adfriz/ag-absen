<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataPelajaran extends Model
{
    use HasFactory;

    protected $table = 'mata_pelajaran';

    protected $fillable = [
        'nama',
        'kode',
    ];

    /**
     * Relasi ke Jadwal pelajaran yang menggunakan mata pelajaran ini
     */
    public function jadwal(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'mata_pelajaran_id');
    }
}
