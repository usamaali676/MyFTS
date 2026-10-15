<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
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
