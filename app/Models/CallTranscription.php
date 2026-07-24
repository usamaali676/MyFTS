<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CallTranscription extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'agent_user_id',
        'agent_name_snapshot',
        'original_filename',
        'duration_seconds',
        'file_size_bytes',
        'mime_type',
        'status',
        'transcript_json',
        'word_count',
        'exchange_count',
        'processing_time_ms',
        'error_message',
        'openai_audio_tokens',
        'created_by',
    ];

    protected $casts = [
        'transcript_json' => 'array',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
