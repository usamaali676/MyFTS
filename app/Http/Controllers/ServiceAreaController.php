<?php

namespace App\Http\Controllers;

use App\Models\ServiceArea;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class ServiceAreaController extends Controller
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
                'country' => 'required',
                'states' => 'required',
                'sale_id4' => 'required',
                'cities' => 'required', // Include cities if it's mandatory
            ], [
                'country.required' => 'The country field is required.',
                'states.required' => 'The states field is required.',
                'sale_id4.required' => 'The sale ID is required.',
                'cities.required' => 'The city field is required.',
            ]);

            $servicearea = ServiceArea::create([
                'sale_id' => $request->sale_id4,
                'country' => $request->country,
                'state' => $request->states,
                'city' => $request->cities,
            ]);

            return response()->json([
                'message' => 'Area Created Successfully!',
                'servicearea' => $servicearea
            ], 200);
        } catch (Exception $e) {
            // Log the exception for debugging
            Log::error($e->getMessage());

            return response()->json([
                'error' => 'An error occurred while creating the area. Please try again.'
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceArea $serviceArea)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceArea $serviceArea)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceArea $serviceArea)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $area = ServiceArea::find($request->id);
        $area->delete();
        return response()->json([
           'message' => 'Area deleted successfully!'
        ], 200);
    }
}
