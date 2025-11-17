<?php
// File: database/migrations/xxxx_xx_xx_xxxxxx_create_kinerja_capaian_details_table.php

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
        // Tabel ini akan menyimpan 13+ baris indikator untuk setiap laporan
        Schema::create('kinerja_capaian_details', function (Blueprint $table) {
            $table->id();

            // Kunci asing yang terhubung ke tabel 'laporan_kinerja'
            // Pastikan tabel 'laporan_kinerja' sudah dibuat oleh migrasi sebelumnya
            $table->foreignId('laporan_kinerja_id')
                  ->constrained('laporan_kinerja') // Merujuk ke tabel 'laporan_kinerja'
                  ->onDelete('cascade'); // Jika laporan dihapus, detail ikut terhapus

            $table->string('indikator_name');
            $table->integer('target_sasaran')->nullable();

            // Kolom untuk 12 bulan (gunakan default 0 agar tidak null)
            $table->integer('bln_1')->default(0);
            $table->integer('bln_2')->default(0);
            $table->integer('bln_3')->default(0);
            $table->integer('bln_4')->default(0);
            $table->integer('bln_5')->default(0);
            $table->integer('bln_6')->default(0);
            $table->integer('bln_7')->default(0);
            $table->integer('bln_8')->default(0);
            $table->integer('bln_9')->default(0);
            $table->integer('bln_10')->default(0);
            $table->integer('bln_11')->default(0);
            $table->integer('bln_12')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kinerja_capaian_details');
    }
};