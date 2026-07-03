<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'report_date',
        'prompt_template',
        'final_prompt',
        'user_message',
        'ai_response',
        'model',
        'tokens_used',
        'status',
        'error_message',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
