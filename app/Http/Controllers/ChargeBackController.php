<?php

namespace App\Http\Controllers;

use App\Models\ChargeBack;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChargeBackController extends Controller
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
        try {
            $request->validate([
                'invoice_id' => 'required',
                'claim_date' => 'required',
                'merchant_id' => 'required',
            ]);
            $old_chargeBack = ChargeBack::where('invoice_id', $request->invoice_id)->first();
            if ($old_chargeBack) {
                return response()->json([
                    'error' => 'ChargeBack for this invoice already exists!',
                ], 409);
            }
            $charge = ChargeBack::create([
                'lead_id' => $request->input('lead_id'),
                'invoice_id' => $request->input('invoice_id'),
                'claim_date' => $request->input('claim_date'),
                'merchant_id' => $request->input('merchant_id'),
                'claim_reason' => $request->input('chargeBack_reason'),
            ]);
            $chargeBack = ChargeBack::where('lead_id', '=', $charge->lead_id)->with('invoice', 'merchant')->get();
            return response()->json([
                'message' => 'ChargeBack Added Successfully!',
                'chargeBack' => $chargeBack
            ], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
            ], 422);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(ChargeBack $chargeBack)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChargeBack $chargeBack)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ChargeBack $chargeBack)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChargeBack $chargeBack)
    {
        //
    }
}
