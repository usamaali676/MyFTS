<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\BankAccount;
use App\Models\Breaks;
use App\Models\Cashapp;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Role;
use App\Models\SubCategory;
use App\Models\User;
use App\Models\ZelleAccount;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CallBackNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use MehediJaman\LaravelZkteco\LaravelZkteco;
use Rats\Zkteco\Lib\ZKTeco;
use App\Services\ZKTecoTCP;
use OpenAI\Laravel\Facades\OpenAI;


class FrontController extends Controller
{

public function generate(Request $request)
{
    $request->validate([
        'date' => 'required|date'
    ]);
    $request->date = Carbon::now();

    $fixedPrompt = "
    You are an expert assistant.

    Report me with the weather of Miami of Date:
    ";

    $prompt = $fixedPrompt . "\n\nDate: " . $request->date;

    $result = OpenAI::responses()->create([
        'model' => 'gpt-4.1-mini',
        'input' => $prompt,
    ]);
    $response = $result->output_text;
    dd($response);

    return response()->json([
        'success' => true,
        'response' => $response,
    ]);
}
    public function get_subcategory(Request $request){
        if($request->ajax()){
            $sub_category = SubCategory::where('business_category_id' , $request->selected)->get();
            return response()->json(['sub_category' => $sub_category]);
        }
    }


            public function search(Request $request)
            {
                $searchTerm = $request->input('query');

                // Search Clients
                $users = User::where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                    ->get();

                // Search Services
                $role = Role::where('name', 'LIKE', "%{$searchTerm}%")->get();

                $lead = Lead::where('business_name_adv', 'LIKE', "%{$searchTerm}%")
                ->orWhere('business_number_adv', 'LIKE', "%{$searchTerm}%")
                ->orwhere('off_email', 'LIKE', "%{$searchTerm}%")
                ->get();

                $invoice = Invoice::where('invoice_number', 'LIKE', "%{$searchTerm}%")->get();


                // Search Keywords
                // $keywords = Keyword::where('word', 'LIKE', "%{$searchTerm}%")->get();

                // Search Service Areas
                // $serviceAreas = ServiceArea::where('area_name', 'LIKE', "%{$searchTerm}%")->get();

                // Combine results into one array
                $results = [
                    'users' => $users,
                    'role' => $role,
                    'leads' => $lead,
                    'invoice' => $invoice,
                    // 'keywords' => $keywords,
                    // 'serviceAreas' => $serviceAreas,
                    // 'keywords' => $keywords,
                    // 'serviceAreas' => $serviceAreas,
                ];

                // Return results as JSON for use in frontend (e.g., autocomplete or live search)
                return response()->json($results);
            }

            public function getCountries()
            {
                // Replace with your logic to fetch countries, e.g., from a JSON file or database
                $countries = json_decode(file_get_contents(storage_path('app/areas.json')), true);
                $special_countries = [];
                foreach ($countries as $country)
                {
                    if($country['id'] == 1 || $country['id'] == 2){
                        $special_countries[] =  $country;
                    }
                }
                return response()->json($special_countries);
            }

            public function getStates($countryId)
            {
                $data = json_decode(file_get_contents(storage_path('app/areas.json')), true);

                // Check if countries are structured properly
                foreach ($data as $country) {
                    if ($country['name'] == $countryId) {
                        $states = $country['states'] ?? []; // Use null coalescing to avoid errors
                        return response()->json($states);
                    }
                }

                return response()->json(['error' => 'Country not found.'], 404);
            }

            public function getCities($stateId, $conrtyId)
            {
                $data = json_decode(file_get_contents(storage_path('app/areas.json')), true);

                // Since there's no 'countries' key, access 'states' directly


                $cities = [];

                foreach ($data as $country) {
                    if ($country['name'] == $conrtyId) {
                        $states = $country['states'] ?? [];
                        foreach ($states as $state) {
                            if ($state['name'] == $stateId) {
                                $cities = $state['cities']?? []; // Use null coalescing to avoid errors
                                return response()->json($cities);
                            }
                        }
                    }
                }

                return response()->json(['error' => 'Cities not found.'], 404);
            }
            public function getInvoicePrice(Request $request)
            {
                if($request->ajax()){
                    $invoice = Invoice::where('id' , $request->id)->first();
                    // dd($invoice->id);
                    $payment = Payment::where('invoice_id', $invoice->id)->orderBy('id', 'desc') // Specify your custom column
                    ->first();
                    return response()->json(['invoice' => $invoice, 'payment' => $payment]);
                }
            }

            public function getInvoice($invoiceNumber)
            {
                $invoice = Invoice::where('invoice_number', $invoiceNumber)->first();
                if ($invoice) {
                    return view('pages.invoice.view', compact('invoice'));
                }
            }
            public function invoicePrint($invoiceNumber)
            {
                $invoice = Invoice::where('invoice_number', $invoiceNumber)->first();
                if ($invoice) {
                    return view('pages.invoice.print', compact('invoice'));
                }
            }

            public function getzelle(){
                $zelle = ZelleAccount::all();
                return response()->json(['zelle' => $zelle]);
            }
            public function getcash(){
                $cash = Cashapp::all();
                return response()->json(['cash' => $cash]);
            }
            public function getbank(){
                $bank = BankAccount::all();
                return response()->json(['bank' => $bank]);
            }
            public function getRefund(Request $request)
            {
                if($request->ajax()){
                    // $invoice = Invoice::where('id', $request->invoice_id)->first();
                    $payment = Payment::where('id', $request->payment_id)->with('invoice')->first();
                    // dd($invoice->id);
                    return response()->json(['payment' => $payment]);
                }
            }
            public function getchargeBack(Request $request)
            {
                if($request->ajax()){
                    // $invoice = Invoice::where('id', $request->invoice_id)->first();
                    $payment = Payment::where('id', $request->payment_id)->with('invoice')->first();
                    // dd($invoice->id);
                    return response()->json(['payment' => $payment]);
                }
            }

            public function showVerifyForm()
            {
                return view('auth.otp');
            }

                private function getShiftDate()
                {
                    $now = now('Asia/Karachi');

                    // Shift: 7 PM → 4 AM
                    if ($now->hour < 5) {
                        return $now->subDay()->toDateString();
                    }

                    return $now->toDateString();
                }
            public function cronlogout()
            {
                $now = now('Asia/Karachi');
                    // Only run at 04:15
                    if ($now->format('H:i') !== '04:15') {
                        return response()->json(['message' => 'Skipped']);
                    }
                $shiftDate = $this->getShiftDate();
                $attendances = Attendance::whereNull('logout_time')
                    ->where('shift_date', '=', $shiftDate)
                    ->get();
                    $logoutTime = now('Asia/Karachi');


                foreach ($attendances as $attendance) {
                     if ($logoutTime->lessThan($attendance->login_time)) {
                            $logoutTime->addDay();
                    }
                    $attendance->logout_time = $logoutTime;
                    $attendance->working_minutes = Carbon::parse($attendance->login_time)->diffInMinutes($logoutTime);
                    $attendance->save();
                }
                  DB::table('sessions')->truncate();

                return response()->json(['message' => 'Cron logout executed successfully.']);
            }



            public function attendancefilter(Request $request)
            {

                    // Get request parameters
                    $dateRange = $request->input('date'); // expected format: "2026-02-01 to 2026-02-21"
                    $agentId = $request->input('agent');
                    $type = $request->input('type');

                    // Base query
                    $query = Attendance::query();

                    // Filter by agent if provided
                    if (!empty($agentId)) {
                        $query->where('user_id', $agentId);
                    }
                    // dd($query->get());

                    // Filter by type if provided
                    //  if (!empty($type)) {
                    //     if ($type === 'late') {
                    //         $query->where('is_late', 1);
                    //     } elseif ($type === 'half') {
                    //         $query->where('half_day', 1);
                    //     }
                    // }
                    // if (!empty($type)) {
                    //  if ($type === 'late') {
                    //         $query->where('is_late', 1);
                    //     }
                    // }
                    // // dd(vars: $query->get());

                    // if (!empty($type)) {
                    //     if ($type === 'half') {
                    //         $query->where('half_day', 1);
                    //     }
                    // }
                    // dd($type);

                    $query->when($type === 'Late', function ($q) {
                        $q->where('is_late', 1);
                    })->when($type === 'Half-Day', function ($q) {
                        $q->where('half_day', 1);
                    });


                    // dd(vars: $query->get());

                    // Filter by date range if provided
                    if (!empty($dateRange)) {
                        // Split range
                        $dates = explode(' to ', $dateRange);

                        if (count($dates) === 2) {
                            $startDate = Carbon::parse($dates[0])->startOfDay();
                            $endDate = Carbon::parse($dates[1])->endOfDay();
                            $query->whereBetween('shift_date', [$startDate, $endDate]);
                        }
                    }


                    // Fetch data
                    $attendances = $query->orderBy('shift_date', 'desc')->get();
                                        // dd(vars: $query->get());

                    // Prepare response data
                    $data = $attendances->map(function ($attendance, $index) {
                        return [
                            'sr_no' => $index + 1,
                            'agent' => $attendance->user->name ?? 'N/A', // Assuming Attendance belongsTo Agent
                            'date' => $attendance->shift_date,
                            'login_time' => $attendance->login_time,
                            'logout_time' => $attendance->logout_time,
                            'working_minutes' => $attendance->working_minutes,
                            'late' => $attendance->is_late ? 'Yes' : 'No',
                            'half_day' => $attendance->half_day ? 'Yes' : 'No',
                        ];
                    });
                    // dd($data);

                    return response()->json([
                        'data' => $data,
                        // 'summary' => [/* optional summary data */],
                    ]);

            }

            // public function startBreak(Request $request)
            // {
            //      dd($request->input('break_type'));
            //      $shiftDate = $this->getShiftDate();
            //     $user = Auth::user();

            //     $attendance = Attendance::where('user_id', $user->id)
            //         ->where('shift_date', $shiftDate)
            //         ->first();
            //         // dd($attendance);

            //     // Check if already on break
            //     $activeBreak = Breaks::where('user_id', $user->id)
            //         ->whereNull('break_end')
            //         ->first();

            //     if ($activeBreak) {
            //         Alert::error('Error', 'Already on break');
            //         return redirect()->back();
            //     }

            //     Breaks::create([
            //         'user_id' => $user->id,
            //         'attendance_id' => $attendance->id,
            //         'break_start' => Carbon::parse($shiftDate)
            //         ->setTimeFrom(now('Asia/Karachi')),
            //     ]);
            // }


            public function startBreak(Request $request)
{
    try {
        $breakType = $request->input('break_type');
        $shiftDate = $this->getShiftDate();
        $user = Auth::user();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('shift_date', $shiftDate)
            ->first();

        // Catch the most common causes of 500
        if (!$attendance) {
            return response()->json(['error' => 'No attendance record found for today'], 404);
        }

        if (!$breakType) {
            return response()->json(['error' => 'Break type is missing'], 422);
        }

        $activeBreak = Breaks::where('user_id', $user->id)
            ->whereNull('break_end')
            ->first();

        if ($activeBreak) {
            return response()->json(['error' => 'Already on break'], 400);
        }

        Breaks::create([
            'user_id'       => $user->id,
            'attendance_id' => $attendance->id,
            'break_type'    => $breakType,
            'break_start'   => Carbon::parse($shiftDate)->setTimeFrom(now('Asia/Karachi')),
        ]);

        return response()->json(['success' => true, 'break_type' => $breakType]);

    } catch (\Exception $e) {
        return response()->json([
            'error'   => $e->getMessage(),
            'line'    => $e->getLine(),
            'file'    => $e->getFile(),
        ], 500);
    }
}
            public function endBreak()
            {
                $shiftDate = $this->getShiftDate();
                $user = Auth::user();

                $break = Breaks::where('user_id', $user->id)
                    ->whereNull('break_end')
                    ->latest()
                    ->first();
                    $breakStart = Carbon::parse($break->break_start, 'Asia/Karachi');
                    $breakEnd = Carbon::parse($shiftDate)
                    ->setTimeFrom(now('Asia/Karachi'));

                if ($break) {
                    $break->break_end = $breakEnd;
                    $break->duration = $breakStart->diffInSeconds($breakEnd);
                    $break->save();
                }
            }

            public function ZktecoInteg() {
                $zk = new LaravelZkteco('192.168.99.18', 4370);  // Remove 'TCP' — not supported in this lib
                $connected = $zk->connect();

                if ($connected) {
                    sleep(1);

                    // ✅ These actually exist in MehediJaman\LaravelZkteco
                    $serial  = $zk->serialNumber();
                    $name    = $zk->deviceName();
                    $version = $zk->fmVersion();

                    dd([
                        'serial'   => $serial,
                        'name'     => $name,
                        'version'  => $version,
                    ]);
                }
                else {
                    echo "Failed to connect.";
                }
            }

            public function ZktecoRawTest() {
                $ip   = '192.168.99.18';
                $port = 4370;

                // ZKTeco "connect" command (UDP)
                $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
                socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, ['sec' => 3, 'usec' => 0]);

                $command = pack('SSII', 1000, 0, 0, 0); // CMD_CONNECT

                socket_sendto($socket, $command, strlen($command), 0, $ip, $port);

                $response = '';
                $from     = '';
                $fromPort = 0;

                $received = socket_recvfrom($socket, $response, 1024, 0, $from, $fromPort);
                socket_close($socket);

                dd([
                    'bytes_received' => $received,
                    'raw_hex'        => $received ? bin2hex($response) : 'NO RESPONSE',
                ]);
            }

            public function ZktecoTCPTest() {
    $ip   = '192.168.99.18';
    $port = 4370;

    $socket = @fsockopen($ip, $port, $errno, $errstr, 5);

    if ($socket) {
        fclose($socket);
        dd('✅ TCP Connection successful — device speaks TCP');
    } else {
        dd("❌ TCP also failed: [$errno] $errstr");
    }
}
    public function ZktecoIntegs() {
        $zk = new ZKTeco('192.168.99.18', 4370);

        // Force TCP mode
        $zk->connect();

        sleep(1);

        $zk->disableDevice();

        $users      = $zk->getUser();
        $attendance = $zk->getAttendance();

        $zk->enableDevice();
        $zk->disconnect();

        dd([
            'users_count'      => count($users),
            'attendance_count' => count($attendance),
            'users'            => $users,
            'attendance'       => $attendance,
        ]);
    }

    public function ZktecoIntegnew()
{
   // commKey = 0 is factory default — change if you set a password on the device
    $zk   = new ZKTecoTCP('192.168.99.18', 4370, 0);
     $zk->debugAuth();
    //  dd('Debug complete — check output above');
    $connected = $zk->connect();

    dd([
        'connected'        => $connected,
        'users_count'      => $connected ? count($zk->getUsers())      : 'N/A',
        'attendance_count' => $connected ? count($zk->getAttendance()) : 'N/A',
        'users'            => $connected ? $zk->getUsers()             : [],
        'attendance'       => $connected ? $zk->getAttendance()        : [],
    ]);
}
public function ZktecoDebug()
{
    $ip   = '192.168.99.18';
    $port = 4370;

    // ── Step 1: Raw TCP open ──────────────────────────────────
    $socket = @fsockopen($ip, $port, $errno, $errstr, 10);

    if (!$socket) {
        dd("❌ Step 1 FAILED — fsockopen: [$errno] $errstr");
    }

    stream_set_timeout($socket, 5);
    echo "✅ Step 1 PASSED — TCP socket opened<br>";

    // ── Step 2: Build CMD_CONNECT packet ─────────────────────
    $command   = 1000; // CMD_CONNECT
    $sessionId = 0;
    $replyId   = 0;

    // Build payload without checksum first
    $buf      = pack('vvvv', $command, 0, $sessionId, $replyId);

    // Calculate checksum
    $sum = 0;
    $padded = str_pad($buf, ceil(strlen($buf) / 2) * 2, "\x00");
    for ($i = 0; $i < strlen($padded); $i += 2) {
        $sum += unpack('v', $padded[$i] . $padded[$i+1])[1];
    }
    while ($sum >> 16) $sum = ($sum & 0xFFFF) + ($sum >> 16);
    $checksum = ~$sum & 0xFFFF;

    // Rebuild with real checksum
    $buf       = pack('vvvv', $command, $checksum, $sessionId, $replyId);
    $tcpHeader = "\x50\x50\x82\x7d" . pack('V', strlen($buf));
    $packet    = $tcpHeader . $buf;

    echo "✅ Step 2 PASSED — Packet built: " . bin2hex($packet) . "<br>";

    // ── Step 3: Send packet ───────────────────────────────────
    $written = @fwrite($socket, $packet);

    if ($written === false || $written === 0) {
        dd("❌ Step 3 FAILED — Could not write to socket");
    }

    echo "✅ Step 3 PASSED — Sent $written bytes<br>";

    // ── Step 4: Read raw response (no parsing) ────────────────
    usleep(500000); // wait 500ms

    $raw = @fread($socket, 1024);

    if ($raw === false || $raw === '') {
        // Try reading in chunks
        $raw = '';
        for ($i = 0; $i < 5; $i++) {
            $chunk = @fread($socket, 256);
            if ($chunk) { $raw .= $chunk; break; }
            usleep(200000);
        }
    }

    echo "✅ Step 4 — Raw response hex: <b>" . ($raw ? bin2hex($raw) : 'EMPTY — NO RESPONSE') . "</b><br>";
    echo "✅ Step 4 — Raw length: " . strlen($raw) . " bytes<br>";

    // ── Step 5: Parse whatever came back ─────────────────────
    if (strlen($raw) >= 8) {
        $magic = substr($raw, 0, 4);
        echo "Magic bytes: " . bin2hex($magic) . " (expected: 50508277 or 50508270)<br>";

        if (strlen($raw) >= 12) {
            $inner   = substr($raw, 8);
            $decoded = unpack('vcommand/vchecksum/vsession/vreply', substr($inner, 0, 8));
            echo "Command: "    . $decoded['command']  . " (2000 = ACK_OK)<br>";
            echo "Session ID: " . $decoded['session']  . "<br>";
        }
    }

    fclose($socket);
    dd('🔍 Debug complete — check output above');
}






    public function callbacknotification()
    {
        $today = Carbon::today()->toDateString();
        // $date = Carbon::parse($datetime)->toDateString();
        $lead = Lead::whereDate('call_back_time', $today)->get();

            foreach ($lead as $leads ) {
                // dd($leads->closers());
                $closers = $leads->closers()->get(); // Get all the closers related to the lead
                $userIds = $closers->pluck('closer_id'); // Extract user IDs from the closers
                $users = User::whereIn('id', $userIds)->get(); // Get users from closers
                // dd($users);
                Notification::send($users, new CallBackNotification($users, $lead));
            }


    }

    }


