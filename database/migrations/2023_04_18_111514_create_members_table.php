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
        Schema::create('members', function (Blueprint $table) {
            $table->id('id');
            $table->string('nomor_member');
            $table->unsignedBigInteger('id_user')->nullable();
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->string('nama_member');
            $table->string('email_member');
            $table->string('nomor_telepon_member');
            $table->string('tanggal_lahir_member');
            $table->string('alamat_member');
            $table->integer('sisa_deposit_reguler');
            $table->date('masa_berlaku_member')->nullable()->default(null);
            $table->string('status_member');
            $table->string('username_member');
            $table->string('password_member');
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
        Schema::dropIfExists('members');
    }
};
