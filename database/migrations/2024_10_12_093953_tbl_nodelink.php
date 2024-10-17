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
        Schema::create('tbl_nodelink', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kontrak_nodelink_id');
            $table->string('sid');
            $table->string('service_line');
            $table->timestamp('created_date')->useCurrent();
            $table->unsignedBigInteger('workorder_nodelink_id');
            $table->string('status_nodelink');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_nodelink');
    }
};
