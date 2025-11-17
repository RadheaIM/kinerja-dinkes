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
        Schema::table('administrasi_tu', function (Blueprint $table) {
            // Tambahkan kolom setelah 'tahun', beri default 'puskesmas'
            $table->string('jenis_laporan')->default('puskesmas')->after('tahun');
            // Index untuk performa query (opsional tapi bagus)
            $table->index(['puskesmas_name', 'tahun', 'jenis_laporan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('administrasi_tu', function (Blueprint $table) {
            // Cek index sebelum drop untuk menghindari error jika index tidak ada
             if (Schema::hasIndex('administrasi_tu', ['puskesmas_name', 'tahun', 'jenis_laporan'])) {
                 $table->dropIndex(['puskesmas_name', 'tahun', 'jenis_laporan']);
             }
            $table->dropColumn('jenis_laporan');
        });
    }
};
