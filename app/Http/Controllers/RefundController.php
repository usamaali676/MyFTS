<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Refund;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RefundController extends Controller
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
        try {
            $request->validate([
                'refund_type' => 'required',
                'invoice_id' =>'required',
                'refund_amount' =>'required',
                'merchant_id' =>'required',
            ]);
            $invoice = Invoice::where('id', $request->invoice_id)->first();

            if ( $request->refund_amount > $invoice->total_amount ) {
                return response()->json([
                    'error' => 'Charge Back Amount Exceeded',
                ], 422);
            }
           $refund = Refund::create([
                'refund_type' => $request->refund_type,
                'invoice_id' => $request->invoice_id,
                'refund_amount' => $request->refund_amount,
               'merchant_id' => $request->merchant_id,
               'claim_date' => $request->claim_date,
               'refund_reason' => $request->claim_reason,
               'lead_id' => $request->lead_id,
            ]);
            $refunds = Refund::where('lead_id', '=', $refund->lead_id)->with('invoice', 'merchant')->get();
            return response()->json([
                'message' => 'Refund Added Successfully!',
                'refunds' => $refunds
            ], 200);
        } catch (Exception $e) {
            // Log the exception for debugging
            Log::error($e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
            ], 422);
        }


    }

    /**
     * Display the specified resource.
     */
    public function show(Refund $refund)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Refund $refund)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Refund $refund)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Refund $refund)
    {
        //
    }
}
