<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Cashapp;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Role;
use App\Models\SubCategory;
use App\Models\User;
use App\Models\ZelleAccount;
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

                    auth()->login($user);
                    return redirect('/');
                }

                return back()->withErrors(['otp' => 'Invalid OTP']);
            }

    }


