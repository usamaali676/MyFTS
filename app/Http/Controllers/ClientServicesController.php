<?php

namespace App\Http\Controllers;

use App\Models\ClientServiceCompanyService;
use App\Models\ClientServices;
use App\Models\CompanyServices;
use App\Models\Sale;
use Illuminate\Http\Request;

class ClientServicesController extends Controller
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
    public function search_services(Request $request){
        $query = $request->input('query');

        // Fetch matching items based on the search term
        $items = ClientServices::where('name', 'LIKE', "%{$query}%")->get();

        return response()->json($items);

    }
    public function sync_comp_services(Request $request)
    {
        // dd($request->all());
        // Validate the request inputs
        $validated = $request->validate([
            'client_service' => 'required|array',
            'company_service' => 'required|array',
        ]);

        foreach ($request->input('client_service') as $index => $clientServiceId) {
            // Get the company services for the current client service
            $companyServices = $request->input('company_service')[$index] ?? [];

            // Ensure $companyServices is an array
            if (is_array($companyServices)) {
                // Delete all existing relationships for this client service
                ClientServiceCompanyService::where('client_service_id', $clientServiceId)->delete();

                // Insert the new selected company services
                foreach ($companyServices as $companyServiceId) {
                    ClientServiceCompanyService::create([
                        'client_service_id' => $clientServiceId,
                        'company_service_id' => $companyServiceId,
                    ]);
                }
            }
        }
        return response()->json([
            'message' => 'Service Added Succesfully!',
        ], 200);


    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'service_name' => 'required|string|max:255',
        ]);

        if (isset($request->sale_id2)) {
            $service = ClientServices::where('name', '=', $request->service_name)->first();

            if ($service === null)  {

                $service = ClientServices::create([
                    'name' => $request->input('service_name')
                ]);
            }
            $sale = Sale::find($request->sale_id2);
            $sale->clientServices()->syncWithoutDetaching([$service->id]);
            $client_service = $sale->clientServices()->with('companyServices')->get();;

            $company_services = CompanyServices::all();
            $client_company_services = $sale->clientServices()->with('companyServices')->get();

            return response()->json([
                'message' => 'Service Added Succesfully!',
                'service' => $service,
                'company_services' => $company_services,
                'client_service' => $client_service,
                'client_company_services' => $client_company_services
            ], 200);
        } else {
            return response()->json([
                'error' => 'Please Create Sale First!',
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ClientServices $clientServices)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClientServices $clientServices)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClientServices $clientServices)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClientServices $clientServices)
    {
        //
    }
}
