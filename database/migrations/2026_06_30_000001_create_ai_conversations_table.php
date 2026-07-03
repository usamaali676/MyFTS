<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('session_id')->index();
            $table->date('report_date')->nullable();
            $table->longText('prompt_template');
            $table->longText('final_prompt');
            $table->longText('user_message');
            $table->longText('ai_response')->nullable();
            $table->string('model')->nullable();
            $table->unsignedInteger('tokens_used')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->longText('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_conversations');
    }
};
