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
        Schema::create('tbl_proforma_invoice', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('no_proforma_invoice');
            $table->timestamp('tanggal_proforma')->useCurrent();
            $table->dateTime('tanggal_jatuh_tempo');
            $table->integer('biaya_perangkat');
            $table->integer('deposit_layanan')->nullable();
            $table->integer('biaya_pengiriman')->nullable();
            $table->integer('ppn')->nullable();
            $table->integer('total_keseluruhan');
            $table->string('url_proforma')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_proforma_invoice');
    }
};
