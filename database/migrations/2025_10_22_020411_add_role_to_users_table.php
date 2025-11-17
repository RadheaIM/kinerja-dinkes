<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk menambahkan kolom baru ke tabel users.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom role dan nama_puskesmas
            $table->string('role')->default('puskesmas')->after('password'); // admin / puskesmas
            $table->string('nama_puskesmas')->nullable()->after('role');
        });
    }

    /**
     * Batalkan migrasi (rollback).
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom jika rollback
            $table->dropColumn(['role', 'nama_puskesmas']);
        });
    }
};
