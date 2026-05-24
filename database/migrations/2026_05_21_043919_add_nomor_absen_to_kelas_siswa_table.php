<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kelas_siswa', function (Blueprint $table) {
            $table->integer('nomor_absen')->nullable()->after('tahun_ajaran_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas_siswa', function (Blueprint $table) {
            $table->dropColumn('nomor_absen');
        });
    }
};
