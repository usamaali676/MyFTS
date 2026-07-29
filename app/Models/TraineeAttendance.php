<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TraineeAttendance extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'trainee_id',
        'date',
        'present',
        'late',
        'created_by_user_id',
    ];

    protected $casts = [
        'date' => 'date',
        'present' => 'boolean',
        'late' => 'boolean',
    ];

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(Trainee::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
