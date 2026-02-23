<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\BankAccount;
use App\Models\Cashapp;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Role;
use App\Models\SubCategory;
use App\Models\User;
use App\Models\ZelleAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FrontController extends Controller
{
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




    }


