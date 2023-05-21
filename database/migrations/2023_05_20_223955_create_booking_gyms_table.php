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
        Schema::create('booking_gyms', function (Blueprint $table) {
            $table->string('nomor_booking_gym')->primary();
            $table->unsignedBigInteger('id_member');
            $table->foreign('id_member')->references('id')->on('members')->onDelete('cascade');
            $table->dateTime('tanggal_booking_gym');
            $table->string('tanggal_pelaksanaan_gym');
            $table->string('jam_sesi_booking_gym');
            $table->integer('kapasitas_gym')->nullable();
            $table->time('jam_presensi_gym')->nullable();
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
        Schema::dropIfExists('booking_gyms');
    }
};
