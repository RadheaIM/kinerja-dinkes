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
    public function up()
    {
        Schema::create('rhk_kapus', function (Blueprint $table) {
    $table->id();
    $table->string('rhk_kadis');
    $table->string('rhk_kapus');
    $table->string('indikator_kinerja');
    $table->string('aspek');
    $table->string('target_tahunan');
    $table->string('rencana_aksi');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rhk_kapus');
    }
};
