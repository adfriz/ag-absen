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
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('jadwal_id')->constrained('jadwal')->cascadeOnDelete();
            $table->date('tanggal');
            $table->enum('status', ['H', 'S', 'I', 'A', 'D', 'T']); // Hadir, Sakit, Izin, Alpa, Dispensasi, Terlambat
            $table->text('catatan')->nullable(); // Keterangan opsional / alasan dispensasi
            $table->integer('menit_terlambat')->nullable(); // Opsional untuk status Terlambat
            $table->string('bukti_surat')->nullable(); // Bukti surat sakit/izin dari siswa
            $table->foreignId('diabsen_oleh')->constrained('users')->cascadeOnDelete(); // Guru yang menginput absensi
            $table->timestamps();

            // Mencegah duplikasi data absensi siswa pada jadwal dan tanggal yang sama
            $table->unique(['siswa_id', 'jadwal_id', 'tanggal'], 'presensi_unik_per_hari');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};
