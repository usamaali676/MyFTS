<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

/**
 * Builds one user's attendance calendar for a month (week grid, punch
 * records, and On-Time / Late / Absent counts). Shared by the single-agent
 * and the department-wise calendar views so both always agree.
 */
class AttendanceCalendarService
{
    public function build(User $user, Carbon $month): array
    {
        $records = Attendance::where('user_id', $user->id)
            ->whereYear('shift_date', $month->year)
            ->whereMonth('shift_date', $month->month)
            ->get()
            ->keyBy(fn ($record) => Carbon::parse($record->shift_date)->format('Y-m-d'));

        $calendarStart = $month->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        // Weeks run Sunday–Saturday, so the grid must end on a Saturday
        // (endOfWeek takes the week's *last* day) — SUNDAY here added an
        // extra, entirely out-of-month row to every calendar.
        $calendarEnd = $month->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        $today = now('Asia/Karachi')->startOfDay();
        $joinedAt = Carbon::parse($user->created_at, 'Asia/Karachi')->startOfDay();

        $weeks = [];
        $cursor = $calendarStart->copy();
        while ($cursor->lte($calendarEnd)) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $week[] = $cursor->copy();
                $cursor->addDay();
            }
            $weeks[] = $week;
        }

        $onTimeCount = $lateCount = $absentCount = $halfDayCount = 0;

        foreach ($weeks as $week) {
            foreach ($week as $day) {
                if ($day->month !== $month->month) {
                    continue;
                }

                $record = $records->get($day->format('Y-m-d'));

                if ($record) {
                    $record->is_late ? $lateCount++ : $onTimeCount++;
                    if ($record->half_day) {
                        $halfDayCount++;
                    }
                } elseif ($day->isWeekend()) {
                    // Weekends aren't working days, so a missing punch isn't an absence.
                } elseif ($day->lt($joinedAt)) {
                    // The user's account didn't exist yet on this day.
                } elseif ($day->lt($today)) {
                    // No punch recorded for a day that has already fully passed: absent.
                    // Today is excluded — the shift may not have started/finished yet.
                    $absentCount++;
                }
            }
        }

        return [
            'user' => $user,
            'weeks' => $weeks,
            'records' => $records,
            'joinedAt' => $joinedAt,
            'onTimeCount' => $onTimeCount,
            'lateCount' => $lateCount,
            'absentCount' => $absentCount,
            'halfDayCount' => $halfDayCount,
        ];
    }
}
