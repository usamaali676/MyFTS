<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('call_transcriptions', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->foreignId('agent_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('agent_name_snapshot');
            $table->string('original_filename');
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->unsignedBigInteger('file_size_bytes');
            $table->string('mime_type');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->json('transcript_json')->nullable();
            $table->unsignedInteger('word_count')->nullable();
            $table->unsignedInteger('exchange_count')->nullable();
            $table->unsignedInteger('processing_time_ms')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedInteger('openai_audio_tokens')->nullable();
            $table->string('compliance_status')->nullable();
            $table->longText('compliance_html')->nullable();
            $table->json('compliance_summary')->nullable();
            $table->string('compliance_error')->nullable();
            $table->json('compliance_turns')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_transcriptions');
    }
};
