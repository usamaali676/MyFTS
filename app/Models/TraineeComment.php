<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TraineeComment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'trainee_id',
        'user_id',
        'comment',
        'comment_date',
    ];

    protected $casts = [
        'comment_date' => 'datetime',
    ];


    public function trainee(): BelongsTo
    {
        return $this->belongsTo(Trainee::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
