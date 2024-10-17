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
        Schema::create('tbl_order', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('layanan_id');
            $table->unsignedBigInteger('alamat_customer_id')->nullable();
            $table->integer('quantity');
            $table->dateTime('order_date');
            $table->integer('total_harga');
            $table->dateTime('tanggal_pembayaran')->nullable();
            $table->unsignedBigInteger('riwayat_status_order_id');
            $table->string('unique_order');
            $table->string('snap_token')->nullable();
            $table->string('payment_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_order');
    }
};
