<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Breaks extends Model
{
    protected $fillable = [
        'user_id',
        'attendance_id',
        'break_start',
        'break_end',
        'break_type',
        'duration',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
