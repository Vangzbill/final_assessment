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
        Schema::create('tbl_invoice', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kontrak_detail_layanan_id');
            $table->unsignedBigInteger('kontrak_nodelink_id');
            $table->dateTime('tanggal_invoice')->nullable();
            $table->dateTime('tanggal_jatuh_tempo')->nullable();
            $table->string('url_bukti_potong')->nullable();
            $table->string('url_invoice')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_invoice');
    }
};
