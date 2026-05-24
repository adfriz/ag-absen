<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Jadwal: widget filters by hari + user_id
        Schema::table('jadwal', function (Blueprint $table) {
            $table->index('hari');
            $table->index(['user_id', 'hari']);
        });

        // Presensi: widget eager-loads by jadwal_id + tanggal
        Schema::table('presensi', function (Blueprint $table) {
            $table->index(['jadwal_id', 'tanggal']);
        });

        // Substitusi: queried by guru_pengganti_id + tanggal
        Schema::table('substitusi_jadwal', function (Blueprint $table) {
            $table->index(['guru_pengganti_id', 'tanggal']);
        });

        // Hari Libur: always queried by tanggal
        Schema::table('hari_libur', function (Blueprint $table) {
            $table->index('tanggal');
        });

        // Izin Guru: queried by user_id + status + date range
        Schema::table('izin_guru', function (Blueprint $table) {
            $table->index(['user_id', 'status', 'tanggal_mulai', 'tanggal_selesai'], 'izin_guru_lookup_idx');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal', function (Blueprint $table) {
            $table->dropIndex(['hari']);
            $table->dropIndex(['user_id', 'hari']);
        });
        Schema::table('presensi', function (Blueprint $table) {
            $table->dropIndex(['jadwal_id', 'tanggal']);
        });
        Schema::table('substitusi_jadwal', function (Blueprint $table) {
            $table->dropIndex(['guru_pengganti_id', 'tanggal']);
        });
        Schema::table('hari_libur', function (Blueprint $table) {
            $table->dropIndex(['tanggal']);
        });
        Schema::table('izin_guru', function (Blueprint $table) {
            $table->dropIndex('izin_guru_lookup_idx');
        });
    }
};
