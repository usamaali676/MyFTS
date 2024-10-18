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
        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->enum('social_name', ['Website','LinkedIn' ,'Facebook' ,'Instagram' ,'TikTok', 'Youtube', 'Twitter', 'GMB', 'Yelp',]);
            $table->string('social_link');
            $table->foreign('sale_id')->references('id')->on('sales');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_links');
    }
};
