<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            $table->string('kategori')->nullable()->after('judul'); // contoh: Capaian Program, Administrasi
            $table->string('wilayah')->nullable()->after('kategori'); // contoh: Puskesmas Cicalengka, Labkesda
        });
    }

    public function down(): void
    {
        Schema::table('laporansphp artisan migrate:fresh
', function (Blueprint $table) {
            $table->dropColumn(['kategori', 'wilayah']);
        });
    }
};
