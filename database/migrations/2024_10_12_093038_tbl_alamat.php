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
        Schema::create('tbl_alamat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('alamat')->nullable();
            $table->string('provinsi_id');
            $table->string('kota_id');
            $table->string('kecamatan_id');
            $table->string('kelurahan_id');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->integer('rt')->nullable();
            $table->integer('rw')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_alamat');
    }
};
