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
        Schema::table('laporans', function (Blueprint $table) {
            // Tambahkan kolom pegawai_id jika belum ada
            if (!Schema::hasColumn('laporans', 'pegawai_id')) {
                $table->unsignedBigInteger('pegawai_id')->nullable()->after('id');

                // Hubungkan dengan tabel pegawais (pastikan tabel pegawais sudah ada)
                $table->foreign('pegawai_id')->references('id')->on('pegawais')->onDelete('cascade');
            }
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            if (Schema::hasColumn('laporans', 'pegawai_id')) {
                $table->dropForeign(['pegawai_id']);
                $table->dropColumn('pegawai_id');
            }
        });
    }
};
