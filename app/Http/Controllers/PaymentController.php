<?php

namespace App\Http\Controllers;

use App\Helpers\GlobalHelper;
use App\Models\Client;
use App\Models\Holidays;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\User;
use App\Notifications\NewPaymentNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required',
            'merchant' => 'required',
            'mop' => 'required',
            'payment_amount' => 'required',
            'trans_id' => 'required',
            'trans_ss' => 'required',
        ]);
        $payment_amount = Payment::where('invoice_id', $request->invoice_id)->where('payment_type', "Partials Payment")->first();
        // dd($payment_amount);
        $full_payment = Payment::where('invoice_id', $request->invoice_id)->where('payment_type', "Full Payment")->first();
        // dd($full_payment);
        $invoice = Invoice::where('id', $request->invoice_id)->first();
        // dd($invoice);
        $last_balnce = Payment::where('invoice_id', $invoice->id)->where('payment_type',"Partials Payment")->orderBy('id', 'desc') // Specify your custom column
        ->first();
        // dd($last_balnce);
        if(isset($last_balnce) ){
            if($last_balnce->balance > 0){
            $balance = $last_balnce->balance - $request->payment_amount;
            }
            else{
                return response()->json([
                    'error' => 'Invoice Amount Exceeded',
                ], 422);
            }
        }
        else{
            $balance = $invoice->total_amount - $request->payment_amount;
        }

        // dd($invoice->total_amount);
        if ($request->payment_amount > $invoice->total_amount ) {
            return response()->json([
                'error' => 'Invalid Amount',
            ], 422);
        }
        elseif(isset($full_payment)){
            return response()->json([
                'error' => 'Payment Already Charged',
            ], 422);
        }
        elseif(isset($payment_amount) && $request->payment_amount > $payment_amount->balance){
            return response()->json([
                'error' => 'Invoice Amount Exceeded',
            ], 422);
        }
        else{
            $payment = new Payment();
            $payment->invoice_id = $request->invoice_id;
            $payment->invoice_number = $invoice->invoice_number;
            $payment->merchant_id = $request->merchant;
            $payment->mop = $request->mop;
            $payment->payment_type = $request->payment_type;
            if(isset($request->card_number)){
                $payment->card_number = $request->card_number;

            }
            if(isset($request->paypal_email)){
                $payment->paypal_email = $request->paypal_email;

            }
            if(isset($request->zelle_id)){
                $payment->zelle_id = $request->zelle_id;

            }
            if(isset($request->cashapp_id)){
                $payment->cashapp_id = $request->cashapp_id;

            }
            if(isset($request->bank_transfer_id)){
                $payment->bank_transfer_id = $request->bank_transfer_id;

            }
            $payment->amount = $request->payment_amount;
            $payment->balance = $balance;
            $payment->trans_id = $request->trans_id;
            if(isset($request->trans_ss)){
            $payment->trans_ss	= GlobalHelper::fts_upload_img($request->trans_ss, 'recipts' );
            }
            $payment->save();

            $sale = Sale::where('id', $invoice->sale_id)->first();
            $sale->update([
                'status' => true,
                // 'activation_date' => Carbon::now(),
            ]);
            $activationDate = $sale->activation_date;

            $activationDate = Carbon::parse($activationDate);

            // Step 2: Set the reporting date to 4 days after the same day in the next month
            $reportingDate = $activationDate->addMonth()->day($activationDate->day)->addDays(4);  // 4 days after the same day of the next month

            // Step 3: Check if the day is Saturday or Sunday
            while ($reportingDate->isWeekend()) {
                // If it's Saturday or Sunday, move to the next Monday
                $reportingDate->next(Carbon::MONDAY);
            }

            // Step 4: Get all holiday dates from the database
            $holidays = Holidays::pluck('date')->toArray(); // Get holidays in 'Y-m-d' format

            // Step 5: Check if the reporting date is in the holiday list
            while (in_array($reportingDate->toDateString(), $holidays)) {
                // If the date is a holiday, shift to the next working day
                $reportingDate->addDay();

                // Ensure the new date is not a weekend (Saturday/Sunday)
                while ($reportingDate->isWeekend()) {
                    $reportingDate->addDay();
                }
            }

            // Final reporting date
            $finalReportingDate = $reportingDate->toDateString();
            $ext_client = Client::where('sale_id',  $sale->id)->first();
            if($ext_client == null) {
                $client = Client::create([
                    'sale_id' => $sale->id,
                    'status' => true,
                    'reporting_date' => $finalReportingDate,
                ]);
            }
            $invoice = Invoice::find($request->invoice_id);
            $title = 'A Payment Charged Against Invoice: '. $invoice->invoice_number;

            $relatedUsers = User::WhereHas('role', function ($query) {
                    $query->whereIn('name', ['QA', 'Executives', 'Creator', 'Accounts']);
                })
                ->get();
                foreach ($relatedUsers as $user) {
                    $user->notify(new NewPaymentNotification($invoice, $payment, $title));
                }
            $all_invoices =  Invoice::where('sale_id', $sale->id)->get();
            $invoiceIds = $all_invoices->pluck('id')->toArray();
            $payments = Payment::whereIn('invoice_id', $invoiceIds)
            ->with('invoice', 'merchant')// Fixed 'merchant' to 'merchant'
            ->get();

            return response()->json([
                'message' => 'Payment Charged Successfully',
                'current_payment' => $payment,
                'payments' => $payments,
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
