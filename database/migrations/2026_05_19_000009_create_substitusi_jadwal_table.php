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
        Schema::create('substitusi_jadwal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('izin_guru_id')->nullable()->constrained('izin_guru')->cascadeOnDelete();
            $table->foreignId('jadwal_id')->constrained('jadwal')->cascadeOnDelete();
            $table->date('tanggal');
            $table->foreignId('guru_pengganti_id')->constrained('users')->cascadeOnDelete(); // Guru pengganti
            $table->timestamps();

            // Mencegah ada lebih dari satu guru pengganti untuk jadwal dan hari yang sama
            $table->unique(['jadwal_id', 'tanggal'], 'substitusi_unik_per_pertemuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('substitusi_jadwal');
    }
};
