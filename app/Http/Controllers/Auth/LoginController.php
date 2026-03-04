<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Notifications\OtpNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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



         // Admin & Executives skip OTP
        if ($user->email === 'admin@admin.com' ||
            optional($user->role)->name === "Executives") {

            $this->markAttendance($user);
            return redirect()->intended($this->redirectPath());
        }


        // Generate a 6-digit OTP for other users
        $otp = random_int(100000, 999999);

        // Save OTP to the user's record
        $user->otp = $otp;
        $user->save();

        // Send OTP to the admin email
        Mail::raw("OTP of $user->name is: $otp", function ($message) {
            $message->to(['umair@firmtechsol.com', 'email@crm.firmtechllc.com'])
                    ->subject('Login OTP');
        });
        if ($user->slack_member_id) {
            $user->notify((new OtpNotification($user))->locale('es'));
        }
        // Log out the user and redirect to the OTP verification page
        Auth::logout();
        session(['user_id' => $user->id]); // Store user ID in session
        return redirect()->route('front.otp.verify');
    }

                public function verify(Request $request)
            {
                $request->validate([
                    'otp' => 'required|digits:6',
                ]);

                $user = User::find(session('user_id'));

                if ($user && $user->otp == $request->otp) {
                    // Clear OTP and log in the user
                    $user->otp = null;
                    $user->save();

                     Auth::login($user);
                    $this->markAttendance($user);
                    return redirect('/');
                }

                return back()->withErrors(['otp' => 'Invalid OTP']);
            }
     /*
    |--------------------------------------------------------------------------
    | MARK ATTENDANCE (Night Shift Safe)
    |--------------------------------------------------------------------------
    */


        // protected function markAttendance($user)
        // {
        //     $now = Carbon::now(config('app.timezone'));
        //     $currentTime = $now->format('H:i:s');

        //     // Shift timings
        //     $shiftStart = Carbon::createFromTime(19, 0, 0, config('app.timezone'));
        //     $lateThreshold = Carbon::createFromTime(19, 1, 0, config('app.timezone'));   // 7:01 PM
        //     $halfDayThreshold = Carbon::createFromTime(20, 0, 0, config('app.timezone')); // 8:00 PM

        //     // Determine correct shift_date (Night Shift 7 PM - 4 AM)
        //     if ($now->hour >= 19) {
        //         $shiftDate = $now->toDateString();
        //     } else {
        //         $shiftDate = $now->copy()->subDay()->toDateString();
        //     }

        //     // Prevent duplicate attendance
        //     $attendance = Attendance::where('user_id', $user->id)
        //         ->where('shift_date', $shiftDate)
        //         ->first();

        //     if ($attendance) {
        //         return; // Already marked — do nothing
        //     }

        //     // Default values
        //     $isLate = false;
        //     $halfDay = false;

        //     // Apply rules only if login after shift start
        //     if ($now->greaterThanOrEqualTo($lateThreshold)) {
        //         $isLate = true;
        //     }

        //     if ($now->greaterThanOrEqualTo($halfDayThreshold)) {
        //         $halfDay = true;
        //         $isLate = true; // half day automatically late
        //     }

        //     Attendance::create([
        //         'user_id'    => $user->id,
        //         'shift_date' => $shiftDate,
        //         'login_time' => $currentTime,
        //         'is_late'    => $isLate,
        //         'half_day'   => $halfDay,
        //     ]);
        // }

        protected function markAttendance($user)
        {
            $now = now('Asia/Karachi');
            $shiftDate = $this->getShiftDate();

            $attendance = Attendance::where('user_id', $user->id)
                ->where('shift_date', $shiftDate)
                ->first();

            if ($attendance) {
                return;
            }

            // Create real shift start datetime (IMPORTANT)
            $shiftStart = Carbon::parse($shiftDate . ' 19:00:00', 'Asia/Karachi');
            $lateThreshold = $shiftStart->copy()->addMinute();      // 7:01 PM
            $halfDayThreshold = $shiftStart->copy()->addHour();     // 8:00 PM

            $isLate = $now->greaterThanOrEqualTo($lateThreshold);
            $halfDay = $now->greaterThanOrEqualTo($halfDayThreshold);

            Attendance::create([
                'user_id'    => $user->id,
                'shift_date' => $shiftDate,
                'login_time' => $now, // store full datetime
                'is_late'    => $isLate,
                'half_day'   => $halfDay,
            ]);
        }

    /*
    |--------------------------------------------------------------------------
    | NIGHT SHIFT DATE CALCULATION
    |--------------------------------------------------------------------------
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



    /*
    |--------------------------------------------------------------------------
    | LOGOUT WITH WORKING HOURS
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
        {
            $user = Auth::user();

            if ($user) {

                $shiftDate = $this->getShiftDate();

                $attendance = Attendance::where('user_id', $user->id)
                    ->where('shift_date', $shiftDate)
                    ->first();


                if ($attendance && !$attendance->logout_time) {


                    $loginTime = Carbon::parse($attendance->login_time, 'Asia/Karachi');
                    $logoutTime = now('Asia/Karachi');

                    $minutes = $loginTime->diffInMinutes($logoutTime, false);

                    if ($minutes < 0) {
                        $minutes += 1440;
                    }

                    $hours = floor($minutes / 60);

                    $attendance->logout_time = $logoutTime;
                    $attendance->working_minutes = $hours;
                    $attendance->save();
                }
            }

            Auth::logout();
            return redirect('/login');
        }
}
