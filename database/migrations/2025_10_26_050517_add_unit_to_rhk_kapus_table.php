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
            // Tambahkan kolom untuk mengaitkan data dengan unit puskesmas/labkesda
            if (!Schema::hasColumn('rhk_kapus', 'unit')) {
                $table->string('unit')->nullable()->after('id');
            }

            // Tambahkan kolom status jika nanti kamu mau atur data ini tampil otomatis
            if (!Schema::hasColumn('rhk_kapus', 'status')) {
                $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->after('unit');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rhk_kapus', function (Blueprint $table) {
            $table->dropColumn(['unit', 'status']);
        });
    }
};
