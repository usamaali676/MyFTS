<?php

namespace App\Http\Controllers;

use App\Helpers\GlobalHelper;
use App\Models\Attendance;
use App\Models\Breaks;
use App\Models\Role;
use App\Models\User;
use App\Services\AttendanceCalendarService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, AttendanceCalendarService $calendar)
    {
        $sr = 1;
        $user = User::all();
        $attendances = Attendance::orderBy('id', 'desc')->get();

        $view = $request->query('view') === 'calendar' ? 'calendar' : 'list';

        // The calendar pickers only offer active users; an inactive user's
        // calendar still renders if reached directly via ?agent=ID.
        $activeUsers = $user->where('status', 1)->values();

        $calendarUserId = $request->query('agent');
        $calendarUser = $calendarUserId ? $user->firstWhere('id', (int) $calendarUserId) : null;

        $departmentId = $request->query('department');
        $department = null;
        $departmentCalendars = collect();

        $departments = Role::whereIn('id', $activeUsers->pluck('role_id')->unique())
            ->orderBy('name')
            ->get();

        $month = $request->query('month')
            ? Carbon::createFromFormat('Y-m', $request->query('month'))->startOfMonth()
            : now()->startOfMonth();

        $weeks = [];
        $records = collect();
        $onTimeCount = $lateCount = $absentCount = $halfDayCount = 0;
        $joinedAt = null;

        if ($departmentId) {
            $department = $departments->firstWhere('id', (int) $departmentId);

            if ($department) {
                $calendarUser = null;
                $calendarUserId = null;
                $departmentCalendars = $activeUsers
                    ->where('role_id', $department->id)
                    ->sortBy('name')
                    ->values()
                    ->map(fn (User $member) => $calendar->build($member, $month));
            }
        }

        if ($calendarUser) {
            ['weeks' => $weeks, 'records' => $records, 'joinedAt' => $joinedAt,
             'onTimeCount' => $onTimeCount, 'lateCount' => $lateCount, 'absentCount' => $absentCount,
             'halfDayCount' => $halfDayCount]
                = $calendar->build($calendarUser, $month);
        }

        return view('pages.attendance', compact(
            'attendances', 'sr', 'user', 'activeUsers', 'view', 'calendarUser', 'calendarUserId', 'month', 'weeks', 'records',
            'onTimeCount', 'lateCount', 'absentCount', 'halfDayCount', 'joinedAt',
            'departments', 'department', 'departmentCalendars'
        ));
    }

    /**
     * Every break one user took on one date. "Date" means the SHIFT date
     * (the night shift runs 7 PM -> 4 AM, so breaks after midnight belong to
     * the shift that started the evening before), matching the Attendance
     * list and calendar.
     */
    public function dailyBreaks(Request $request)
    {
        $users = User::orderBy('name')->get(['id', 'name', 'status', 'role_id']);

        try {
            $date = $request->query('date')
                ? Carbon::createFromFormat('Y-m-d', $request->query('date'))->format('Y-m-d')
                : GlobalHelper::getShiftDate();
        } catch (\Throwable $e) {
            $date = GlobalHelper::getShiftDate();
        }

        $target = $request->query('user') ? $users->firstWhere('id', (int) $request->query('user')) : null;

        $attendance = null;
        $breaks = collect();

        if ($target) {
            $attendance = Attendance::where('user_id', $target->id)->where('shift_date', $date)->first();

            $breaks = Breaks::where('user_id', $target->id)
                ->whereHas('attendance', fn ($q) => $q->where('shift_date', $date))
                ->orderBy('break_start')
                ->get();
        }

        // Lengths come from the break's clock times (Breaks::effective_duration),
        // wrapping past midnight, not the saved `duration` column — older rows
        // saved a wrong value for any break that crossed midnight. Only
        // finished, believable breaks count toward totals; the rest are
        // surfaced as data issues instead of silently skewing the numbers.
        $valid = $breaks->filter(fn ($b) => $b->is_countable);
        $seconds = fn ($b) => $b->effective_duration;
        $stats = [
            'count' => $breaks->count(),
            'total_seconds' => (int) $valid->sum($seconds),
            'longest_seconds' => (int) ($valid->max($seconds) ?? 0),
            'average_seconds' => $valid->isNotEmpty() ? (int) round($valid->sum($seconds) / $valid->count()) : 0,
            'open' => $breaks->filter(fn ($b) => !$b->break_end)->count(),
            'invalid' => $breaks->filter(fn ($b) => $b->break_end && !$b->is_countable)->count(),
            'by_type' => $valid->groupBy('break_type')->map(fn ($g) => [
                'count' => $g->count(),
                'seconds' => (int) $g->sum($seconds),
            ]),
        ];

        $dateCarbon = Carbon::createFromFormat('Y-m-d', $date);

        return view('pages.attendance-breaks', compact('users', 'target', 'date', 'dateCarbon', 'attendance', 'breaks', 'stats'));
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
