<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }


    protected function authenticated(Request $request, $user)
    {
        // Check if the user's email is admin@admin.com
        if ($user->email === 'admin@admin.com') {
            // Skip 2FA and redirect to the intended page
            return redirect()->intended($this->redirectPath());
        }

        // Generate a 6-digit OTP for other users
        $otp = random_int(100000, 999999);

        // Save OTP to the user's record
        $user->otp = $otp;
        $user->save();

        // Send OTP to the admin email
        Mail::raw("Your OTP is: $otp", function ($message) {
            $message->to('umair@firmtechsol.com')
                    ->subject('Login OTP');
        });

        // Log out the user and redirect to the OTP verification page
        auth()->logout();
        session(['user_id' => $user->id]); // Store user ID in session
        return redirect()->route('front.otp.verify');
    }
}
