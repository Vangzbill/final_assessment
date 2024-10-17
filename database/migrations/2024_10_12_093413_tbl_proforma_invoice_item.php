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
        Schema::create('tbl_proforma_invoice_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('keterangan')->nullable();
            $table->timestamp('tanggal')->useCurrent();
            $table->unsignedBigInteger('layanan_id');
            $table->unsignedBigInteger('proforma_invoice_id');
            $table->integer('nilai_pokok');
            $table->integer('nilai_ppn');
            $table->integer('total_bayar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_proforma_invoice_item');
    }
};
