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
        Schema::create('transaksi_deposit_regulers', function (Blueprint $table) {
            $table->string('nomor_struk_deposit_reguler')->primary();
            $table->unsignedBigInteger('id_pegawai');
            $table->foreign('id_pegawai')->references('id')->on('pegawais')->onDelete('cascade');
            $table->unsignedBigInteger('id_member');
            $table->foreign('id_member')->references('id')->on('members')->onDelete('cascade');
            $table->unsignedBigInteger('id_promo')->nullable();
            $table->foreign('id_promo')->references('id')->on('promos')->onDelete('cascade');
            $table->dateTime('tanggal_deposit_reguler');
            $table->integer('nominal_deposit_reguler');
            $table->integer('bonus_deposit_reguler');
            $table->integer('total_deposit_reguler');
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
        Schema::dropIfExists('transaksi_deposit_regulers');
    }
};
