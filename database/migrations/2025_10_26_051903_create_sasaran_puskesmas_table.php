<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('sasaran_puskesmas', function (Blueprint $table) {
            $table->id();
            $table->string('puskesmas');
            $table->integer('bumil')->nullable();
            $table->integer('bulin')->nullable();
            $table->integer('bbl')->nullable();
            $table->string('balita')->nullable(); // contoh "400/420"
            $table->integer('pendidikan_dasar')->nullable();
            $table->integer('uspro')->nullable();
            $table->integer('lansia')->nullable();
            $table->integer('hipertensi')->nullable();
            $table->integer('dm')->nullable();
            $table->integer('odgj_berat')->nullable();
            $table->integer('tb')->nullable();
            $table->integer('hiv')->nullable();
            $table->integer('idl')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Hapus tabel.
     */
    public function down(): void
    {
        Schema::dropIfExists('sasaran_puskesmas');
    }
};
