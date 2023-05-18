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
        Schema::create('transaksi_aktivasis', function (Blueprint $table) {
            $table->string('nomor_struk_transaksi_aktivasi')->primary();
            $table->unsignedBigInteger('id_pegawai');
            $table->foreign('id_pegawai')->references('id')->on('pegawais')->onDelete('cascade');
            $table->unsignedBigInteger('id_member');
            $table->foreign('id_member')->references('id')->on('members')->onDelete('cascade');
            $table->date('tanggal_transaksi_aktivasi');
            $table->integer('nominal_transaksi_aktivasi');
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
        Schema::dropIfExists('transaksi_aktivasis');
    }
};
