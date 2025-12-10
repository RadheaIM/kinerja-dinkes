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
        Schema::table('rhk_kapus', function (Blueprint $table) {
            // Cek dulu apakah kolom 'tahun' sudah ada atau belum
            if (!Schema::hasColumn('rhk_kapus', 'tahun')) {
                // Jika belum ada, buat kolomnya
                $table->year('tahun')->nullable()->after('id'); 
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rhk_kapus', function (Blueprint $table) {
            if (Schema::hasColumn('rhk_kapus', 'tahun')) {
                $table->dropColumn('tahun');
            }
        });
    }
};