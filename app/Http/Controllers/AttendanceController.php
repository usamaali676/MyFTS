<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sr = 1;
        $user = User::all();
        $attendances = Attendance::orderBy('id', 'desc')->get();

        $view = $request->query('view') === 'calendar' ? 'calendar' : 'list';
        $calendarUserId = $request->query('agent');
        $calendarUser = $calendarUserId ? $user->firstWhere('id', (int) $calendarUserId) : null;

        $month = $request->query('month')
            ? Carbon::createFromFormat('Y-m', $request->query('month'))->startOfMonth()
            : now()->startOfMonth();

        $weeks = [];
        $records = collect();
        $onTimeCount = $lateCount = $absentCount = 0;
        $joinedAt = null;

        if ($calendarUser) {
            $records = Attendance::where('user_id', $calendarUser->id)
                ->whereYear('shift_date', $month->year)
                ->whereMonth('shift_date', $month->month)
                ->get()
                ->keyBy(fn ($record) => Carbon::parse($record->shift_date)->format('Y-m-d'));

            $calendarStart = $month->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
            $calendarEnd = $month->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

            $today = now('Asia/Karachi')->startOfDay();
            $joinedAt = Carbon::parse($calendarUser->created_at, 'Asia/Karachi')->startOfDay();
            $cursor = $calendarStart->copy();
            while ($cursor->lte($calendarEnd)) {
                $week = [];
                for ($i = 0; $i < 7; $i++) {
                    $week[] = $cursor->copy();
                    $cursor->addDay();
                }
                $weeks[] = $week;
            }

            foreach ($weeks as $week) {
                foreach ($week as $day) {
                    if ($day->month !== $month->month) {
                        continue;
                    }

                    $record = $records->get($day->format('Y-m-d'));

                    if ($record) {
                        $record->is_late ? $lateCount++ : $onTimeCount++;
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
        }

        return view('pages.attendance', compact(
            'attendances', 'sr', 'user', 'view', 'calendarUser', 'calendarUserId', 'month', 'weeks', 'records',
            'onTimeCount', 'lateCount', 'absentCount', 'joinedAt'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        //
    }
}
