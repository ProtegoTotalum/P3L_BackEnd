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
        Schema::create('ijin_instrukturs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_instruktur');
            $table->foreign('id_instruktur')->references('id')->on('instrukturs')->onDelete('cascade');
            $table->date('tanggal_pengajuan_ijin');
            $table->date('tanggal_ijin_instruktur');
            $table->string('hari_ijin');
            $table->string('sesi_ijin');
            $table->string('alasan_ijin');
            $table->unsignedBigInteger('id_instruktur_pengganti');
            $table->foreign('id_instruktur_pengganti')->references('id')->on('instrukturs')->onDelete('cascade');
            $table->string('status_konfirmasi');
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
        Schema::dropIfExists('ijin_instrukturs');
    }
};
