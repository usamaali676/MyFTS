<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
       protected $fillable = [
        'user_id',
        'shift_date',
        'login_time',
        'logout_time',
        'is_late',
        'half_day',
    ];

    public function user()
    {
         return $this->belongsTo(User::class, 'user_id');
    }
    public function getFormattedLoginTimeAttribute()
    {
        return Carbon::parse($this->login_time)->format('h:i A');
    }
    public function getFormattedLogoutTimeAttribute()
    {
        return Carbon::parse($this->logout_time)->format('h:i A');
    }
}
