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
        Schema::create('tbl_faq_kategori_produk', function (Blueprint $table) {
            $table->id();
            $table->string('kategori_produk_id');
            $table->string('pertanyaan');
            $table->string('jawaban');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_faq_kategori_produk');
    }
};
