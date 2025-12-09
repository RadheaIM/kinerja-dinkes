<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('sasaran_puskesmas', function (Blueprint $table) {
            // Cek apakah Anda menggunakan year atau integer
            $table->year('tahun')->nullable()->after('puskesmas'); 
            // ATAU
            // $table->integer('tahun')->nullable()->after('puskesmas'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sasaran_puskesmas', function (Blueprint $table) {
            //
        });
    }
};
