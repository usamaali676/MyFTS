<?php

namespace App\Http\Controllers;

use App\Helpers\GlobalHelper;
use Illuminate\Http\Request;

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
        return view('home');
    }
}
