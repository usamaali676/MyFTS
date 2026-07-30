<?php

namespace App\Http\Controllers;

use App\Models\Trainee;
use App\Models\TraineeAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class TraineeAttendanceController extends Controller
{
    public function index($trainee, Request $request)
    {
        $trainee = Trainee::findOrFail($trainee);

        $month = $request->query('month')
            ? Carbon::createFromFormat('Y-m', $request->query('month'))->startOfMonth()
            : now()->startOfMonth();

        $records = TraineeAttendance::where('trainee_id', $trainee->id)
            ->whereYear('date', $month->year)
            ->whereMonth('date', $month->month)
            ->get()
            ->keyBy(fn ($record) => $record->date->format('Y-m-d'));

        $onTimeCount = $records->filter(fn ($r) => $r->present && !$r->late)->count();
        $lateCount = $records->filter(fn ($r) => $r->present && $r->late)->count();
        $absentCount = $records->filter(fn ($r) => !$r->present)->count();
        $markedCount = $records->count();
        $attendanceRate = $markedCount > 0
            ? round((($onTimeCount + $lateCount) / $markedCount) * 100)
            : null;

        $calendarStart = $month->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $calendarEnd = $month->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

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

        return view('pages.trainee-attendance.index', compact(
            'trainee', 'month', 'records', 'weeks',
            'onTimeCount', 'lateCount', 'absentCount', 'markedCount', 'attendanceRate'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'trainee_id' => 'required|exists:trainees,id',
            'date' => 'required|date',
            'status' => 'required|in:on_time,late,absent',
        ]);

        [$present, $late] = match ($request->status) {
            'on_time' => [true, false],
            'late' => [true, true],
            'absent' => [false, false],
        };

        TraineeAttendance::updateOrCreate(
            ['trainee_id' => $request->trainee_id, 'date' => $request->date],
            ['present' => $present, 'late' => $late, 'created_by_user_id' => Auth::id()]
        );

        Alert::success('Success', 'Attendance Saved Successfully');

        return redirect()->route('trainee.index');
    }
}
