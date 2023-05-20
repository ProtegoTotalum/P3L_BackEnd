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
        Schema::create('booking_kelas', function (Blueprint $table) {
            $table->string('nomor_booking_kelas')->primary();
            $table->unsignedBigInteger('id_jadwal_harian');
            $table->foreign('id_jadwal_harian')->references('id')->on('jadwal_harians')->onDelete('cascade');
            $table->unsignedBigInteger('id_member');
            $table->foreign('id_member')->references('id')->on('members')->onDelete('cascade');
            $table->unsignedBigInteger('id_deposit_kelas')->nullable();
            $table->foreign('id_deposit_kelas')->references('id')->on('deposit_kelas')->onDelete('cascade');
            $table->dateTime('tanggal_booking_kelas');
            $table->string('metode_pembayaran_booking_kelas');
            $table->time('jam_presensi_kelas')->nullable();
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
        Schema::dropIfExists('booking_kelas');
    }
};
