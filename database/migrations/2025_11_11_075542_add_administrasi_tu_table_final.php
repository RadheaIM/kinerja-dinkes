<?php
// File: database/migrations/YYYY_MM_DD_create_administrasi_tu_table.php

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
        // PERHATIAN: Nama tabel harus SAMA PERSIS dengan Model Anda (administrasi_tu)
        Schema::create('administrasi_tu', function (Blueprint $table) {
            $table->id();
            
            // Kolom Identitas Laporan
            $table->string('puskesmas_name', 255); 
            $table->year('tahun');
            $table->string('jenis_laporan', 50); // puskesmas / labkesda
            $table->index(['puskesmas_name', 'tahun', 'jenis_laporan']);

            // Kolom Detail Indikator
            $table->string('jenis_layanan_spm', 50)->nullable(); // A, B, C, dst.
            $table->string('indikator', 255);
            
            // PERBAIKAN: Kolom 'deskripsi' dihapus total karena tidak ada di DB lama
            
            $table->string('target', 255)->nullable();

            // Kolom Capaian Bulanan (Dibuat string agar bisa menyimpan 'Ada'/'Tidak')
            for ($i = 1; $i <= 12; $i++) {
                $table->string("bln_$i", 50)->nullable();
            }

            // Kolom Bukti Dukung (Akan disimpan sebagai JSON/text di database)
            $table->text('link_bukti_dukung')->nullable();
            $table->text('file_bukti_dukung')->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('administrasi_tu');
    }
};