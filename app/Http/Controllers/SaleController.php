<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        return view('pages.sale.create', compact('lead', 'client_enum', 'call_enum'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        //
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
