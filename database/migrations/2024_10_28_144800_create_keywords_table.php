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
        Schema::create('keywords', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('area_id');
            $table->string('keyword');
            $table->string('primary')->nullable();
            $table->string('secondary')->nullable();
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete()->constrained()->onDelete('cascade');
            $table->foreign('area_id')->references('id')->on('service_areas')->onDelete()->constrained()->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keywords');
    }
};
