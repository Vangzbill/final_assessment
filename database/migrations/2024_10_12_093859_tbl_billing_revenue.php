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
        Schema::create('tbl_billing_revenue', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kontrak_nodelink_id');
            $table->dateTime('tanggal_tagih')->nullable();
            $table->integer('total_tagihan')->nullable();
            $table->integer('total_ppn')->nullable();
            $table->integer('total_akhir')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_billing_revenue');
    }
};
