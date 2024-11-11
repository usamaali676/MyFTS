<?php

namespace App\Http\Controllers;

use App\Models\ClientServices;
use App\Models\CompanyServices;
use App\Models\Sale;
use App\Models\SaleClientServiceCompanyService;
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
            'sale_id3' => 'required|integer|exists:sales,id',
            'client_service' => 'required|array',
            'company_service' => 'required|array',
        ]);


        foreach ($request->input('client_service') as $index => $clientServiceId) {
            $companyServices = $request->input('company_service')[$index] ?? [];

            if (is_array($companyServices)) {
                // Clear previous associations specific to this sale
                SaleClientServiceCompanyService::where('sale_id', $validated['sale_id3'])
                    ->where('client_service_id', $clientServiceId)
                    ->delete();

                // Save new associations
                foreach ($companyServices as $companyServiceId) {
                    SaleClientServiceCompanyService::create([
                        'sale_id' => $validated['sale_id3'],
                        'client_service_id' => $clientServiceId,
                        'company_service_id' => $companyServiceId,
                    ]);
                }
            }
        }
        $sale = Sale::where('id', $request->sale_id3)->with('companyServices')->first();

        return response()->json([
            'message' => 'Service Added Succesfully!',
            'sale' => $sale,
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
            // $client_service = $sale->clientServices()->with('companyServices')->get();;
            $client_services = $sale->clientServices->map(function ($clientService) use ($sale) {
                // Load the company services specific to the sale
                $clientService->setRelation('companyServicesForSale', $clientService->companyServicesForSale($sale->id)->get());
                return $clientService;
            });

            $company_services = CompanyServices::all();

            return response()->json([
                'message' => 'Service Added Succesfully!',
                'service' => $service,
                'company_services' => $company_services,
                'client_service' => $client_services,
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
    public function destroy( Request $request)
    {
            // Find the client service by ID, return a 404 error if not found
            $client_service = ClientServices::find($request->id);
            // dd($client_service->sales);
            // Delete related sales
            if(isset($client_service)){
                if(isset($client_service->sale))
                {
                    $client_service->sales()->detach();
                }

                $relservices = SaleClientServiceCompanyService::where('client_service_id', $client_service->id)->get();
                foreach ($relservices as $relservice) {
                    $relservice->delete();
                }

                // dd($client_service->companyServicesForSale($client_service->sales->id));
                // Detach or delete company services

                // Finally, delete the client service itself
                $client_service->delete();

                return response()->json([
                    'message' => 'Service Deleted Succesfully!',

                ], 200);
            }
            else {
                return response()->json([
                    'error' => 'Service Already Deleted!',
                ], 422);
            }


    }


}
