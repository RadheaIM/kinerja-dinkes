<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan tabel lama dibuang dulu jika ada sisa-sisa
        Schema::dropIfExists('target_sasarans');

        Schema::create('target_sasarans', function (Blueprint $table) {
            $table->id();
            // INI YANG BENAR: String puskesmas_name, BUKAN Foreign Key
            $table->string('puskesmas_name'); 
            $table->year('tahun');
            $table->string('indikator_name');
            $table->integer('target_value')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('target_sasarans');
    }
};