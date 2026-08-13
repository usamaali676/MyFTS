<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CloseShiftAttendance extends Command
{
    protected $signature = 'attendance:close-shift';

    protected $description = 'Auto clock-out any attendance still open from the previous night shift (7 PM - 4 AM, Asia/Karachi)';

    public function handle(): int
    {
        $now = now('Asia/Karachi');

        // A shift runs 7 PM -> 4 AM the next day. Only auto-close an
        // attendance once its shift has actually finished (shift_date + 1
        // day, 4 AM) — otherwise a stray call to this command mid-shift
        // (e.g. via the public /front/cronlogout endpoint) would force-close
        // and log out every user who is still legitimately clocked in.
        $attendances = Attendance::whereNull('logout_time')
            ->get()
            ->filter(function ($attendance) use ($now) {
                $shiftEnd = Carbon::parse($attendance->shift_date, 'Asia/Karachi')->addDay()->setTime(4, 0);
                return $now->greaterThanOrEqualTo($shiftEnd);
            });

        foreach ($attendances as $attendance) {
            $loginTime = Carbon::parse($attendance->login_time, 'Asia/Karachi');
            $logoutTime = now('Asia/Karachi');

            if ($logoutTime->lessThan($loginTime)) {
                $logoutTime->addDay();
            }

            $attendance->logout_time = $logoutTime;
            $attendance->working_minutes = $loginTime->diffInMinutes($logoutTime);
            $attendance->save();
        }

        if ($attendances->isNotEmpty()) {
            DB::table('sessions')
                ->whereIn('user_id', $attendances->pluck('user_id'))
                ->delete();
        }

        $this->info("Closed {$attendances->count()} open attendance record(s).");

        return self::SUCCESS;
    }
}
