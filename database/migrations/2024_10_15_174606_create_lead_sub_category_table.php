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
        Schema::create('lead_sub_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->references('id')->on('leads')->constrained()->onDelete('cascade');
            $table->foreignId('sub_category_id')->references('id')->on('sub_categories')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_sub_category');
    }
};
