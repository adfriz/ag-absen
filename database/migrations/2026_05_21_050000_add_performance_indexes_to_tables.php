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
        Schema::table('presensi', function (Blueprint $table) {
            $table->index(['jadwal_id', 'tanggal'], 'presensi_jadwal_tanggal_idx');
            $table->index('tanggal', 'presensi_tanggal_idx');
        });

        Schema::table('substitusi_jadwal', function (Blueprint $table) {
            $table->index(['guru_pengganti_id', 'tanggal'], 'substitusi_guru_tanggal_idx');
            $table->index('tanggal', 'substitusi_tanggal_idx');
        });

        Schema::table('jadwal', function (Blueprint $table) {
            $table->index('hari', 'jadwal_hari_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->dropIndex('presensi_jadwal_tanggal_idx');
            $table->dropIndex('presensi_tanggal_idx');
        });

        Schema::table('substitusi_jadwal', function (Blueprint $table) {
            $table->dropIndex('substitusi_guru_tanggal_idx');
            $table->dropIndex('substitusi_tanggal_idx');
        });

        Schema::table('jadwal', function (Blueprint $table) {
            $table->dropIndex('jadwal_hari_idx');
        });
    }
};
