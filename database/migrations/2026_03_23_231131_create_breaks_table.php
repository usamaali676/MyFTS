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
        Schema::create('breaks', function (Blueprint $table) {
            $table->id();
             $table->foreignId('user_id')->constrained()->onDelete('cascade');
             $table->foreignId('attendance_id')->constrained()->onDelete('cascade');
            $table->timestamp('break_start')->nullable();
            $table->timestamp('break_end')->nullable();
            $table->enum('break_type', ['mealBreak', '2ndBreak', 'smokeBreak'])->nullable();
            $table->integer('duration')->nullable(); // in seconds
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breaks');
    }
};
