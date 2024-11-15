<?php

namespace App\Http\Controllers;

use App\Models\BusinessHours;
use App\Models\ClientServices;
use App\Models\CompanyServices;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\LeadCloser;
use App\Models\MerchantAccount;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Sale;
use App\Models\SaleCS;
use App\Models\SocialLink;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {

        $lead = Lead::find($id);
        $client_nature = DB::select("SHOW COLUMNS FROM sales LIKE 'client_nature'");
        $type = $client_nature[0]->Type; // Get the type string
        $client_enum = explode("','", substr($type, 6, -2));

        // $client_enum = explode("','", substr($client_nature[0]->Type, 6, -2), "'");
        $call_type = DB::select("SHOW COLUMNS FROM sales LIKE 'call_type'");
        $call_type = $call_type[0]->Type; // Get the type string
        $call_enum = explode("','", substr($call_type, 6, -2));
        // dd($call_type);
        $social_links = DB::select("SHOW COLUMNS FROM social_links LIKE 'social_name'");
        $social_links = $social_links[0]->Type; // Get the type string
        $social_links = explode("','", substr($social_links, 6, -2));
        $role = Role::where('name', "Closer")->first();
        $closers = User::where('role_id', $role->id)->get();
        $csrole = Role::where('name', "Customer Support")->first();
        $csr = User::where('role_id', $csrole->id)->get();
        $sale = Sale::where('lead_id', $lead->id)->first();
        $mehchant = MerchantAccount::all();


        // dd($sale->social_links);


        $company_services = CompanyServices::all();
        if (isset($sale)) {
            // $lastMonthName = Carbon::now()->subMonth()->format('F');
            // dd($lastMonthName);
            $lastMonthName = Carbon::now()->format('M Y');
            $invoice = Invoice::where('sale_id', $sale->id)->where('month', $lastMonthName)->first();
            $all_invoices = Invoice::where('sale_id', $sale->id)->get();
            if(isset($invoice)){
            $payments = Payment::where('invoice_id', $invoice->id)->get();
            }
            else{
                $payments = NULL;
            }
            // dd($invoice);
            // Get client services for the sale and eager load their company services specific to this sale
            $client_services = $sale->clientServices->map(function ($clientService) use ($sale) {
                // Load the company services specific to the sale
                $clientService->setRelation('companyServicesForSale', $clientService->companyServicesForSale($sale->id)->get());
                return $clientService;
            });
            return view('pages.sale.create', compact('lead', 'client_enum', 'call_enum', 'social_links', 'closers', 'sale', 'company_services', 'client_services', 'invoice', 'mehchant' , 'all_invoices', 'payments', 'csr'));
        } else {
            return view('pages.sale.create', compact('lead', 'client_enum', 'call_enum', 'social_links', 'closers', 'sale', 'company_services', 'mehchant', 'csr'));
        }


        // $client_service = ClientServices::where('id', 12)->first();
        // dd($client_service->companyServices);

    }
    public function sale_info(Request $request){
        // dd($request->all());
        if (isset($request->sale_id)) {
            $sale = Sale::find($request->sale_id);
            $user = Auth::user();
            if($user->role_id == 1 || $user->role->name == "Customer Support"){
                $sale->update([
                    'signup_date' => $request->signup_date,
                    'status' => $request->sale_status  ? 1 : 0,
                ]);
            }
            else{
                $sale->update([
                    'signup_date' => $request->signup_date,
                ]);
            }
            $lead = Lead::find($request->lead_id);
            // dd($lead);
            if (isset($request->closers)) {
                $remainingclosers = LeadCloser::where('lead_id', $lead->id)->get();
                if (isset($remainingclosers)) {
                    foreach($remainingclosers as $closer){
                        $closer->delete();
                    }
                }
                foreach ($request->closers as $users) {
                        LeadCloser::create([
                            'lead_id' => $lead->id,
                            'closer_id' => $users,
                        ]);
                }
            }
            // dd("work");
            if (isset($request->customer_support)) {
                $oldcs = SaleCS::where('sale_id', $sale->id)->get();
                foreach ($oldcs as $cs) {
                    $cs->delete();
                }
                foreach ($request->customer_support as $cs) {
                    SaleCS::create([
                        'sale_id' => $sale->id,
                        'cs_id' => $cs,
                        'created_by' => Auth::user()->id,
                    ]);
                }
            }
            return response()->json([
                'message' => 'Sale Info Updated successfully!',
            ], 200);
        }
        else{
            return response()->json([
                'error' => 'Please Create Sale First!',
            ], 422);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'client_nature' => 'required',
            'call_type' => 'required',
            'timezone' => 'required',
        ]);
        $lead = Lead::find($request->lead_id);
        $lead->update([
            'business_name_adv' => $request->business_name,
            'business_number_adv' => $request->business_number,
            'off_email' => $request->email,
            'website_url' => $request->website_url,
            'client_name' => $request->client_name,
            'updated_by' => Auth::user()->id,
        ]);

        $old_Sale = Sale::where('lead_id', $request->lead_id)->first();
        if ($old_Sale == Null) {
            $sale = Sale::create([
                'lead_id' => $request->lead_id,
                'client_nature' => $request->client_nature,
                'call_type' => $request->call_type,
                'time_zone' => $request->timezone,
                'created_by' => Auth::user()->id,
            ]);
        } else {
            $sale = Sale::where('lead_id', $request->lead_id)->first();
            $sale->client_nature = $request->client_nature;
            $sale->call_type = $request->call_type;
            $sale->time_zone = $request->timezone;
            $sale->updated_by = Auth::user()->id;
            $sale->save();
        }

        $old_social = SocialLink::where('sale_id', $sale->id)->get();
        if ($old_social == Null) {
            if (isset($request->social_name) && isset($request->social_link) && count(array_filter($request->social_name)) > 0) {
                foreach ($request->social_name as $key => $value) {
                    SocialLink::create([
                        'sale_id' => $sale->id,
                        'social_name' => $value,
                        'social_link' => $request->social_link[$key],
                    ]);
                }
            }
        } else {
            $social_links = SocialLink::where('sale_id', $sale->id)->get();
            foreach ($social_links as $social) {
                $social->delete();
            }
            if (isset($request->social_name) && isset($request->social_link) && count(array_filter($request->social_name)) > 0) {
                // dd("dfjhdsflkg");
                foreach ($request->social_name as $key => $value) {
                    SocialLink::create([
                        'sale_id' => $sale->id,
                        'social_name' => $value,
                        'social_link' => $request->social_link[$key],
                    ]);
                }
            }
        }

        if (isset($request->day) && count($request->day) > 0) {
            $days = $request->input('day');
            $openTimes = $request->input('open');
            $closeTimes = $request->input('closed');

            // Checks for each day
            $mondayCheck = $request->input('monday_check');
            $tuesdayCheck = $request->input('tuesday_check');
            $wednesdayCheck = $request->input('wednesday_check');
            $thursdayCheck = $request->input('thursday_check');
            $fridayCheck = $request->input('friday_check');
            $saturdayCheck = $request->input('saturday_check');
            $sundayCheck = $request->input('sunday_check');

            // Array of checks corresponding to each day
            $dayChecks = [
                $mondayCheck, $tuesdayCheck, $wednesdayCheck, $thursdayCheck,
                $fridayCheck, $saturdayCheck, $sundayCheck
            ];

            foreach ($days as $index => $day) {
                $check = $dayChecks[$index];

                // Determine is_closed and is_24/7 flags
                $isClosed = false;
                $is24_7 = false;

                if ($check == 'closed') {
                    $isClosed = true;
                } elseif ($check == '24/7') {
                    $is24_7 = true;
                }

                // Retrieve existing business hours for the day if they exist
                $existingHours = BusinessHours::where('sale_id', $sale->id)->where('day', $day)->first();

                // Default to existing times if provided; otherwise, format new ones
                if (!$isClosed && !$is24_7) {
                    $openingTime = isset($openTimes[$index]) ? $openTimes[$index] : ($existingHours->opening_time ?? '00:00');
                    $closingTime = isset($closeTimes[$index]) ? $closeTimes[$index] : ($existingHours->closing_time ?? '23:59');

                    try {
                        $formattedOpeningTime = Carbon::createFromFormat('H:i', $openingTime)->format('H:i:s');
                        $formattedClosingTime = Carbon::createFromFormat('H:i', $closingTime)->format('H:i:s');
                    } catch (\Exception $e) {
                        // Handle invalid format by using existing or default times
                        $formattedOpeningTime = $existingHours->opening_time ?? '00:00:00';
                        $formattedClosingTime = $existingHours->closing_time ?? '23:59:59';
                    }
                } else {
                    // If closed or 24/7, set default values for opening/closing time
                    $formattedOpeningTime = $existingHours->opening_time ?? '00:00:00';
                    $formattedClosingTime = $existingHours->closing_time ?? '23:59:59';
                }

                // Use updateOrCreate to update if exists or create new if it doesn't
                BusinessHours::updateOrCreate(
                    // Conditions to find the record
                    [
                        'sale_id' => $sale->id,
                        'day' => $day
                    ],
                    // Values to update or create
                    [
                        'opening_time' => $formattedOpeningTime,
                        'closing_time' => $formattedClosingTime,
                        'is_closed' => $isClosed,
                        'is_24/7' => $is24_7
                    ]
                );
            }
        }


        return response()->json([
            'message' => 'Sale added successfully!',
            'sale' => $sale,
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $sale = Sale::find($id);
        return view('pages.sale.view', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        //
    }
}
