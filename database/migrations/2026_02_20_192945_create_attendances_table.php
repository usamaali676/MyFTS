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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('shift_date'); // Important: shift date, NOT system date
            $table->time('login_time')->nullable();  // UTC
            $table->time('logout_time')->nullable(); // UTC
            $table->integer('working_minutes')->nullable();
            $table->boolean('is_late')->default(false);
            $table->boolean('half_day')->default(false);
            $table->timestamps();
            $table->unique(['user_id', 'shift_date']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
