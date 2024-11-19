<?php

namespace App\Http\Controllers;

use App\Models\CompanyServices;
use App\Models\Invoice;
use App\Models\InvoiceServiceCharges;
use App\Models\Sale;
use Carbon\Carbon;
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
        dd($request->all());
        $request->validate([
            'invoice_id' => 'required',
            'invoice_amount' => 'required',
        ]);
        $unique_id = 'FTS'. '_' . Str::random(5) . '_' . time();
        $date = Carbon::now(); // or any other Carbon instance
        $monthName = $date->format('F');
        $invoice = Invoice::where('id', $request->invoice_id)->first();
        $last_invoice = Invoice::where('sale_id', $invoice->sale_id)->where('month', $monthName)->first();
        if($last_invoice == NULL){
            $invoice = Invoice::create([
                'sale_id' => $request->sale_id6,
                'invoice_number' => $unique_id,
                'invoice_active_status' => $request->invoice_status? 1 : 0,
                'activation_date' => $request->activation_date,
                'discount_type' => $request->discount_type,
                'discount_amount' => $request->discount_amount,
                'invoice_due_date' => $request->invoice_due_date,
                'total_amount' => $request->invoice_amount,
                'invoice_freq' =>   $request->invoice_freq,
                'month' => $monthName,
                // 'month' => $monthName
            ]);
        }
        else {
            $invoice->update([
                'discount_type' => $request->discount_type,
                'discount_amount' => $request->discount_amount,
                'invoice_due_date' => $request->invoice_due_date,
                'total_amount' => $request->invoice_amount,
                'invoice_freq' =>   $request->invoice_freq,
                'month' => $monthName,
            ]);
            return response()->json([
                'message' => 'Invoice Update Successfully!',
                'invoice' => $invoice,
            ], 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'sale_id6' => 'required',
            'month' => 'required',
            'activation_date' => 'required',
            'invoice_due_date' => 'required',
        ]);
        $sale = Sale::find($request->sale_id6);
        $unique_id = 'FTS'. '_' . Str::random(5) . '_' . time();
        $date = Carbon::now(); // or any other Carbon instance
        $monthName = $date->format('M Y');
        // dd($monthName);
        $year = $date->format('Y');
        // $company_service = CompanyServices::where('id', $request->company_service_charge)->first();
        // if($company_service->price > $request->amount) {
        //     $discount_amount = $company_service->price - $request->amount;
        // }
        // else {
        //     $discount_amount = 0;
        // }
        if(isset($request->is_complementary)){
            $complementary = $request->is_complementary;
            $charge_price = 0;
        }
        else {
            $complementary = 0;
            $charge_price = $request->amount;
        }
        $invoice = Invoice::where('sale_id', $request->sale_id6)->where('month', $monthName)->first();
        // dd($invoice);
        if(isset($invoice)){
            $invoice->update([
               'invoice_active_status' => $request->invoice_status? 1 : 0,
                'activation_date' => $request->activation_date,
                'discount_type' => $request->discount_type,
                'discount_amount' => $request->discount_amount,
                'invoice_due_date' => $request->invoice_due_date,
                'total_amount' => $request->invoice_amount,
                'invoice_freq' =>   $request->invoice_freq,
                'month' => $request->month,
                // 'year' => $year,
            ]);
        }
        else{
            $invoice = Invoice::create([
                'sale_id' => $request->sale_id6,
                'invoice_number' => $unique_id,
                'invoice_active_status' => $request->invoice_status? 1 : 0,
                'activation_date' => $request->activation_date,
                'discount_type' => $request->discount_type,
                'discount_amount' => $request->discount_amount,
                'invoice_due_date' => $request->invoice_due_date,
                'total_amount' => $request->invoice_amount,
                'invoice_freq' =>   $request->invoice_freq,
                'month' => $request->month,
                // 'year' => $year,
                // 'month' => $monthName
            ]);
        }
        if(isset($request->service_id) && count($request->service_id) > 0){
            foreach($request->service_id as $key => $value){
                $company_service = CompanyServices::where('id', $value)->first();
                if($company_service->price > $request->amount[$key]) {
                    $discount_amount = $company_service->price - $request->amount[$key];
                }
                else {
                    $discount_amount = 0;
                }
                $old_invoice_charge = InvoiceServiceCharges::where('invoice_id', $invoice->id)->where('company_service_id', $value)->where('month', $request->month)->first();
                // dd($old_invoice_charge);
                if($old_invoice_charge == NULL){
                $invoiceServiceCharge = InvoiceServiceCharges::create([
                    'invoice_id' => $invoice->id,
                    'company_service_id' => $value,
                    'shelf_amount' => $company_service->price,
                    'charged_price' => $request->amount[$key],
                    'discount_price' => $discount_amount,
                    'is_complementary' => $request->is_complementary[$key],
                    'month' => $request->month
                ]);
                }
        }
        $all_invoices = $sale->invoice;
        return response()->json([
            'message' => 'Invoice Genrated Succesfully!',
            'invoice' => $invoice->load(['servicecharges.service_name']),
            'all_invoices' => $all_invoices,
        ], 200);
    }
        // if(isset($invoice)){
        //     $last_invoice_charge = InvoiceServiceCharges::where('company_service_id', $request->company_service_charge)->where('month', $monthName)->first();
        //     if($last_invoice_charge == NULL) {
        //         $invoiceServiceCharge = InvoiceServiceCharges::create([
        //             'invoice_id' => $invoice->id,
        //             'company_service_id' => $request->company_service_charge,
        //             'shelf_amount' => $company_service->price,
        //             'charged_price' => $charge_price,
        //             'discount_price' => $discount_amount,
        //             'is_complementary' => $complementary,
        //             'month' => $monthName
        //         ]);
        //         return response()->json([
        //             'message' => 'Item Added Succesfully!',
        //             'invoice' => $invoice->load(['servicecharges.service_name']),
        //         ], 200);
        //     }
        //     else {
        //         return response()->json([
        //             'error' => 'Item Already Exists!',
        //         ], 422);
        //     }
        // }
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
