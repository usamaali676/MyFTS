<?php

namespace App\Http\Controllers;

use App\Helpers\GlobalHelper;
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
    public function index()
    {
        // $route = GlobalHelper::Permissions();
        // dd($route);
        // $user = Auth::user();
        // $totalRevenue =
        // $notifications = Auth::user()->notifications;
        return view('home');
    }
}
