<?php

namespace App\Http\Controllers;

use App\Helpers\GlobalHelper;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Sale;
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
        // dd($request->all());
        $request->validate([
            'invoice_id' => 'required',
            'marchent' => 'required',
            'mop' => 'required',
            'payment_amount' => 'required',
            'trans_id' => 'required',
        ]);
        $payment = Payment::where('invoice_id', $request->invoice_id)->get();
        $invoice = Invoice::find($request->invoice_id);
        if (count($payment) > 0) {
            return response()->json([
                'error' => 'Payment Already Charged',
            ], 422);
        }
        else{
            $payment = new Payment();
            $payment->invoice_id = $request->invoice_id;
            $payment->invoice_number = $invoice->invoice_number;
            $payment->merchant_id = $request->marchent;
            $payment->mop = $request->mop;
            $payment->payment_type = $request->payment_type;
            $payment->amount = $request->payment_amount;
            $payment->card_number = $request->card_number;
            $payment->trans_id = $request->trans_id;
            if(isset($request->trans_ss)){
            $payment->trans_ss	= GlobalHelper::fts_upload_img($request->trans_ss, 'recipts' );
            }
            $payment->save();

            $sale = Sale::where('id', $invoice->sale_id)->first();
            $all_invoices =  Invoice::where('sale_id', $sale->id)->get();
            $invoiceIds = $all_invoices->pluck('id')->toArray();
            $payments = Payment::whereIn('invoice_id', $invoiceIds)->with('invoice', 'marchent' )->get();


            return response()->json([
                'message' => 'Payment Added Successfully',
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
