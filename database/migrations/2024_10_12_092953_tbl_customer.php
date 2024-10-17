<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_customer', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan');
            $table->string('email_perusahaan')->unique();
            $table->string('no_telp_perusahaan');
            $table->string('npwp_perusahaan');
            $table->string('username');
            $table->string('password');
            $table->string('alamat')->nullable();
            $table->string('provinsi_id');
            $table->string('kota_id');
            $table->string('kecamatan_id');
            $table->string('kelurahan_id');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->dateTime('email_verified_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_customer');
    }
};
