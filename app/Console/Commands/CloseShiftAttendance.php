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
        $shiftDate = $now->hour < 5
            ? $now->copy()->subDay()->toDateString()
            : $now->toDateString();

        $attendances = Attendance::whereNull('logout_time')
            ->where('shift_date', $shiftDate)
            ->get();

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

        $this->info("Closed {$attendances->count()} open attendance record(s) for shift {$shiftDate}.");

        return self::SUCCESS;
    }
}
