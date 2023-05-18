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
        Schema::create('deposit_kelas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_member');
            $table->foreign('id_member')->references('id')->on('members')->onDelete('cascade');
            $table->unsignedBigInteger('id_kelas');
            $table->foreign('id_kelas')->references('id')->on('kelas')->onDelete('cascade');
            $table->integer('sisa_deposit_kelas')->nullable();
            $table->date('masa_berlaku_deposit_kelas')->nullable();
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
        Schema::dropIfExists('deposit_kelas');
    }
};
