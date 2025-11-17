<?php
// File: database/migrations/2025_10_27_040805_create_laporan_kinerja_baru_table.php
// (Nama file biarkan saja, yang penting isinya benar)

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
        // === PERBAIKAN: Nama tabel dan kolom yang benar ===
        Schema::create('laporan_kinerja', function (Blueprint $table) { // <-- Nama tabel 'laporan_kinerja'
            $table->id(); // <-- Primary key (UNSIGNED BIGINT)
            $table->string('puskesmas_name');
            $table->year('tahun');
            $table->string('jenis_laporan')->default('capaian_program');
            $table->timestamps();

            // Membuat 'puskesmas_name' dan 'tahun' unik agar tidak duplikat
            $table->unique(['puskesmas_name', 'tahun', 'jenis_laporan']);
        });
        // ================================================
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         // === PERBAIKAN: Nama tabel yang benar ===
        Schema::dropIfExists('laporan_kinerja');
         // ======================================
    }
};