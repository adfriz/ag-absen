<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'tahun',
        'semester',
        'apakah_aktif',
    ];

    protected $casts = [
        'apakah_aktif' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function ($model) {
            if ($model->apakah_aktif) {
                static::where('id', '!=', $model->id)
                    ->where('apakah_aktif', true)
                    ->update(['apakah_aktif' => false]);
            }
        });
    }

    /**
     * Relasi ke Jadwal pelajaran pada tahun ajaran ini
     */
    public function jadwal(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'tahun_ajaran_id');
    }
}
