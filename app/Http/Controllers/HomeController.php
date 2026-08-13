<?php

namespace App\Http\Controllers;

use App\Helpers\GlobalHelper;
use App\Models\Attendance;
use App\Models\Breaks;
use App\Models\CompanyServices;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Role;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
                    private function getShiftDate()
                {
                    $now = now('Asia/Karachi');

                    // Shift: 7 PM → 4 AM
                    if ($now->hour < 5) {
                        return $now->subDay()->toDateString();
                    }

                    return $now->toDateString();
                }
    public function index()
    {
        $route = GlobalHelper::Permissions();
        // dd($route);
        $user = Auth::user();
        // dd($user);
        $lates = Attendance::where('user_id', $user->id)
        ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
        ->where('is_late', true)
        ->count();
        // dd($lates);

        // Days this month, up to (not including) today, with no attendance
        // punch at all and not a weekend — same "absent" rule as the
        // Attendance calendar view.
        $monthStart = now('Asia/Karachi')->startOfMonth();
        $today = now('Asia/Karachi')->startOfDay();
        $markedDates = Attendance::where('user_id', $user->id)
            ->whereYear('shift_date', $monthStart->year)
            ->whereMonth('shift_date', $monthStart->month)
            ->pluck('shift_date')
            ->map(fn ($d) => Carbon::parse($d)->format('Y-m-d'))
            ->flip();

        $absents = 0;
        $cursor = $monthStart->copy();
        while ($cursor->lt($today)) {
            if (!$cursor->isWeekend() && !$markedDates->has($cursor->format('Y-m-d'))) {
                $absents++;
            }
            $cursor->addDay();
        }

        $totalRevenue =
        $notifications = Auth::user()->notifications;
        $notifications->each(function ($notification) {
            $notification->user = User::find($notification->data['added_by']);
        });
        $lead = Lead::where('saler_id', $user->id)
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->get();

        $last_lead = Lead::where('saler_id', $user->id)
        ->whereMonth('created_at',now()->subMonth()->month)
        ->whereYear('created_at', now()->subMonth()->year)
        ->get();

        // Get sales for this month that are associated with those leads
        $sale = Sale::whereIn('lead_id', $lead->pluck('id'))
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->get();

        $sale_count = Sale::whereIn('lead_id', $lead->pluck('id'))
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
        // dd($sale_count);
        $last_sale_count = Sale::whereIn('lead_id', $last_lead->pluck('id'))
        ->whereMonth('created_at', now()->subMonth()->month)  // Subtract 1 month
        ->whereYear('created_at', now()->subMonth()->year)   // Use the previous month year
        ->count();

        // if ($last_sale_count > 0) {
        //     $percentage_diff = (($sale_count - $last_sale_count) / $last_sale_count) * 100;
        // } else {
        //     $percentage_diff = 0;  // Handle case where there were no sales last month
        // }
        // dd($percentage_diff);

        // Sum the invoice amounts for this month
        $total = 0;
        foreach ($sale as $s) {
        $total += Invoice::where('sale_id', $s->id)
                        ->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->sum('total_amount');

        // dd($total );


        }
        // $user = User::all();
        // $role = Role::whereIn('name', ['TSR', 'Closer', 'QA'])->first();
        // $users = User::where('role_id', $role->id)->where('status', 1)->with([
        //     'attendances' => function ($query) {
        //         $query->whereBetween('created_at', [
        //             now()->startOfMonth(),
        //             now()->endOfMonth()
        //         ])->with('breaks');
        //     }
        // ])->get();
        $roleIds = Role::whereIn('name', ['TSR', 'Closer', 'QA'])->pluck('id');

        $users = User::whereIn('role_id', $roleIds)
            ->where('status', 1)
            ->with([
                'attendances' => function ($query) {
                    $query->whereBetween('created_at', [
                        now()->startOfMonth(),
                        now()->endOfMonth()
                    ])->with('breaks');
                }
            ])
            ->get();
        $tsrrole = Role::where('name', 'TSR')->first('id');

        $tsrusers = User::where('role_id', $tsrrole->id)
            ->where('status', 1)
            ->with([
                'attendances' => function ($query) {
                    $query->whereBetween('created_at', [
                        now()->startOfMonth(),
                        now()->endOfMonth()
                    ])->with('breaks');
                }
            ])
            ->get();

        // Team-wide lates/absents, only needed for the Creator/Executives
        // (and user #4) dashboard cards that roll up every TSR's attendance.
        // Each is a per-TSR summary (name + count) that also carries the
        // individual day-by-day records, so the popup can show exactly
        // which date/time each late punch or absence happened on.
        $isTeamView = $user->role->name === 'Creator' || $user->role->name === 'Executives' || $user->id == 4;
        $teamLates = 0;
        $teamAbsents = 0;
        $teamLatesByUser = collect();
        $teamAbsentsByUser = collect();

        if ($isTeamView) {
            $tsrMonthAttendances = Attendance::whereIn('user_id', $tsrusers->pluck('id'))
                ->whereYear('shift_date', $monthStart->year)
                ->whereMonth('shift_date', $monthStart->month)
                ->get()
                ->groupBy('user_id');

            foreach ($tsrusers as $tsrUser) {
                $userAttendances = $tsrMonthAttendances->get($tsrUser->id, collect());

                $userLateRecords = $userAttendances->where('is_late', true)->sortByDesc('shift_date')->values();
                $teamLates += $userLateRecords->count();
                if ($userLateRecords->isNotEmpty()) {
                    $teamLatesByUser->push((object) [
                        'user' => $tsrUser,
                        'count' => $userLateRecords->count(),
                        'records' => $userLateRecords,
                    ]);
                }

                $markedTsrDates = $userAttendances->pluck('shift_date')
                    ->map(fn ($d) => Carbon::parse($d)->format('Y-m-d'))
                    ->flip();

                $userAbsentDates = collect();
                $tsrCursor = $monthStart->copy();
                while ($tsrCursor->lt($today)) {
                    if (!$tsrCursor->isWeekend() && !$markedTsrDates->has($tsrCursor->format('Y-m-d'))) {
                        $userAbsentDates->push($tsrCursor->copy());
                    }
                    $tsrCursor->addDay();
                }
                $teamAbsents += $userAbsentDates->count();
                if ($userAbsentDates->isNotEmpty()) {
                    $teamAbsentsByUser->push((object) [
                        'user' => $tsrUser,
                        'count' => $userAbsentDates->count(),
                        'dates' => $userAbsentDates->sortByDesc(fn ($d) => $d->format('Y-m-d'))->values(),
                    ]);
                }
            }

            $teamLatesByUser = $teamLatesByUser->sortByDesc('count')->values();
            $teamAbsentsByUser = $teamAbsentsByUser->sortByDesc('count')->values();
        }

        $shiftDate = $this->getShiftDate();

        $services = CompanyServices::withCount('leads')->get();

        // Resume an in-progress break on page load instead of relying on
        // client-only state, so a page refresh (e.g. after a session/CSRF
        // expiry) doesn't strand the user with no way to end their break.
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('shift_date', $shiftDate)
            ->first();

        $activeBreak = $todayAttendance
            ? Breaks::where('attendance_id', $todayAttendance->id)->whereNull('break_end')->first()
            : null;

        $activeBreakElapsedSeconds = $activeBreak
            ? Carbon::parse($activeBreak->break_start, 'Asia/Karachi')->diffInSeconds(now('Asia/Karachi'))
            : 0;
        // dd($services);
        // dd($users[1]->attendances->pluck('breaks')->flatten());
        // dd($total);
        return view('home', compact('route', 'notifications', 'totalRevenue', 'sale_count', 'last_sale_count', 'total', 'lates', 'absents', 'isTeamView', 'teamLates', 'teamAbsents', 'teamLatesByUser', 'teamAbsentsByUser', 'users', 'shiftDate', 'services', 'tsrusers', 'activeBreak', 'activeBreakElapsedSeconds'));
    }
    // public function breaksduration()
    // {
    //     $user = Auth::user();
    //     $breaks = Attendance::where('user_id', $user->id)
    //     ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
    //     ->with('breaks')
    //     ->get()
    //     ->pluck('breaks')
    //     ->flatten();
    //     dd($breaks);

    //     return view('breaks_duration', compact('breaks'));
    // }
}
