<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function index() {
        $user = User::all();
        $srno = 1;
        return view('pages.user.index', compact('user', 'srno'));
    }
}
