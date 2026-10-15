<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainee_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainee_id')->constrained('trainees')->cascadeOnDelete();
            $table->date('date');
            $table->boolean('present');
            $table->boolean('late');
            $table->foreignId('created_by_user_id')->constrained('users');
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['trainee_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainee_attendances');
    }
};
