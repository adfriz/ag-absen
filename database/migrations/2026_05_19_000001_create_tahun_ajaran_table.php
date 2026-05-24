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
        Schema::create('tahun_ajaran', function (Blueprint $table) {
            $table->id();
            $table->string('tahun'); // contoh: 2025/2026
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->boolean('apakah_aktif')->default(false);
            $table->timestamps();

            // Mencegah duplikasi tahun & semester yang sama
            $table->unique(['tahun', 'semester'], 'tahun_semester_unik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahun_ajaran');
    }
};
