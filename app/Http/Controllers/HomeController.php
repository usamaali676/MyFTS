<?php

namespace App\Http\Controllers;

use App\Helpers\GlobalHelper;
use App\Models\Attendance;
use App\Models\CompanyServices;
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
                        ->sum('total_amout');



        }
        // $user = User::all();
        $role = Role::where('name', 'TSR')->first();
        $users = User::where('role_id', $role->id)->where('status', 1)->with([
            'attendances' => function ($query) {
                $query->whereBetween('created_at', [
                    now()->startOfMonth(),
                    now()->endOfMonth()
                ])->with('breaks');
            }
        ])->get();
        $shiftDate = $this->getShiftDate();

        $services = CompanyServices::withCount('leads')->get();
        // dd($services);
        // dd($users[1]->attendances->pluck('breaks')->flatten());
        // dd($total);
        return view('home', compact('route', 'notifications', 'totalRevenue', 'sale_count', 'last_sale_count', 'total', 'lates', 'users', 'shiftDate', 'services'));
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
