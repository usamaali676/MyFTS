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

    // Minutes past the 7 PM shift start (see LoginController::markAttendance),
    // used to show "late by Xh Ym" in the UI. Compared by time-of-day only —
    // login_time's calendar date doesn't reliably line up with shift_date + 1
    // day in this data, so a full datetime diff can pick up spurious extra
    // days. Times past midnight (the overnight shift running into the small
    // hours) are treated as a continuation of the same shift.
    public function getLateMinutesAttribute()
    {
        if (!$this->login_time) {
            return null;
        }

        $loginAt = Carbon::parse($this->login_time, 'Asia/Karachi');
        $shiftStartOfDay = 19 * 60; // 7:00 PM, in minutes from midnight
        $loginOfDay = $loginAt->hour * 60 + $loginAt->minute;

        if ($loginOfDay < $shiftStartOfDay) {
            $loginOfDay += 24 * 60;
        }

        return max(0, $loginOfDay - $shiftStartOfDay);
    }
    public function breaks()
    {
        return $this->hasMany(Breaks::class);
    }
}
