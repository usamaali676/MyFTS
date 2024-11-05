<?php

namespace App\Http\Controllers;

use App\Models\CompanyServices;
use App\Models\Invoice;
use App\Models\InvoiceServiceCharges;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvoiceServiceChargesController extends Controller
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
    public function create(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'invoice_id' => 'required',
            'invoice_amount' => 'required',
        ]);
        $invoice = Invoice::where('id', $request->invoice_id)->first();
        $invoice->update([
            'discount_type' => $request->discount_type,
            'discount_amount' => $request->discount_amount,
            'invoice_due_date' => $request->invoice_due_date,
            'total_amount' => $request->invoice_amount,
            'invoice_freq' =>   $request->invoice_freq,
        ]);
        return response()->json([
            'message' => 'Item Added Succesfully!',
            'invoice' => $invoice,

        ], 200);


    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'sale_id6' => 'required',
            'company_service_charge' => 'required',
        ]);
        $unique_id = 'FTS'. Str::random(5) . '_' . time();
        $company_service = CompanyServices::where('id', $request->company_service_charge)->first();
        if($company_service->price > $request->amount) {
            $discount_amount = $company_service->price - $request->amount;
        }
        else {
            $discount_amount = 0;
        }
        if(isset($request->is_complementary)){
            $complementary = $request->is_complementary;
            $charge_price = 0;
        }
        else {
            $complementary = 0;
            $charge_price = $request->amount;
        }
        $invoice = Invoice::where('sale_id', $request->sale_id6)->first();
        if(isset($invoice)){
            $invoice->update([
                'invoice_active_status' => $request->invoice_status? 1 : 0,
                'activation_date' => $request->activation_date,
            ]);
        }
        else{
            $invoice = Invoice::create([
                'sale_id' => $request->sale_id6,
                'invoice_number' => $unique_id,
                'invoice_active_status' => $request->invoice_status? 1 : 0,
                'activation_date' => $request->activation_date,
            ]);
        }

        if(isset($invoice)){
            $invoiceServiceCharge = InvoiceServiceCharges::create([
                'invoice_id' => $invoice->id,
                'company_service_id' => $request->company_service_charge,
                'shelf_amount' => $company_service->price,
                'charged_price' => $charge_price,
                'discount_price' => $discount_amount,
                'is_complementary' => $complementary
            ]);

            return response()->json([
                'message' => 'Item Added Succesfully!',
                'invoice' => $invoice->load(['servicecharges.service_name']),

            ], 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(InvoiceServiceCharges $invoiceServiceCharges)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InvoiceServiceCharges $invoiceServiceCharges)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InvoiceServiceCharges $invoiceServiceCharges)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InvoiceServiceCharges $invoiceServiceCharges)
    {
        //
    }
}
