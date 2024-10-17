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
        Schema::create('tbl_kontrak_nodelink', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kontrak_layanan_id');
            $table->unsignedBigInteger('nodelink_id');
            $table->string('nama_perusahaan');
            $table->string('latitude');
            $table->string('longitude');
            $table->integer('biaya_perangkat');
            $table->integer('biaya_layanan');
            $table->timestamp('created_date')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_kontrak_nodelink');
    }
};
