<?php

namespace App\Http\Controllers;

use App\Models\Keyword;
use App\Models\ServiceArea;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KeywordController extends Controller
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
                'keyword' => 'required|string|max:255',
                'sale_id5' => 'required',
            ]);
            $keyword = Keyword::create([
                'sale_id' => $request->sale_id5,
                'area_id' => $request->area_id,
                'keyword' => $request->keyword,
            ]);

            $area = ServiceArea::find($keyword->area_id);

            return response()->json([
                'message' => 'Keyword Created Successfully!',
                'keyword' => $keyword,
                'area' => $area,
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
    public function show(Keyword $keyword)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Keyword $keyword)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Keyword $keyword)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $keyword = Keyword::find($request->id);
        $keyword->delete();

        // $rem_keyword = K
        return response()->json([
           'message' => 'Keyword deleted successfully!'
        ], 200);
    }
}
