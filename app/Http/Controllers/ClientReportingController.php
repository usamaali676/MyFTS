<?php

namespace App\Http\Controllers;

use App\Helpers\GlobalHelper;
use App\Models\ClientReporting;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientReportingController extends Controller
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
                'report_type' => 'required',
                'client_id' =>'required',
                'report_file' =>'required|file|mimes:pdf,doc,docx|max:2048',
                'created_by' => 'required'
            ]);
            $report = ClientReporting::create([
                'client_id' => $request->client_id,
                'reporting_type' => $request->report_type,
                'created_by' => $request->created_by,
                'report_status' => 'created',
                'created_at' => Carbon::now(),
                'report_file' => GlobalHelper::fts_upload_report($request->report_file, 'report'),
            ]);
            $reports = ClientReporting::where('client_id', $request->client_id)->with('client', 'client.sale.lead', 'createdBy', 'verifiedBy', 'dispatchedBy')->get();
            return response()->json([
               'message' => 'Report created successfully.',
                'reports' => $reports
            ], 201);
        } catch (Exception $e) {
            return response()->json([
               'message' => 'An error occurred while creating the report.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ClientReporting $clientReporting)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $client_report = ClientReporting::find($id);
        $user = Auth::user();
        if(isset($client_report) && $client_report->verified_by != NULL){
            return response()->json([
                'error' => 'Report is Aready Verified!',
            ], 409);
        }
        else{
            $client_report->update([
                'verified_by' => $user->id,
                'report_verified_at' => Carbon::now(),
                'report_status' => 'verified',
            ]);
            $all_reports = ClientReporting::where('client_id', $client_report->client_id)->with('client', 'client.sale.lead', 'createdBy', 'verifiedBy', 'dispatchedBy')->get();
            return response()->json([
                'message' => 'Report Verified Successfully!',
                'reports' => $all_reports,
            ], 200);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( $id)
    {
        $client_report = ClientReporting::find($id);
        // dd($client_report);
        if($client_report->report_status == "created"){
            return response()->json([
                'error' => 'Can not Dispatch Without Varifications',
            ], 409);
        }
        else{
            $user = Auth::user();
            if(isset($client_report) && $client_report->dispatch_at != NULL){
                return response()->json([
                    'error' => 'Report is Already Dispatched!',
                ], 409);
            }
            else{
                $client_report->update([
                    'dispatched_by' => $user->id,
                    'dispatch_at' => Carbon::now(),
                    'report_status' => 'dispatched',
                ]);
                $all_reports = ClientReporting::where('client_id', $client_report->client_id)->with('client', 'client.sale.lead', 'createdBy', 'verifiedBy', 'dispatchedBy')->get();
                return response()->json([
                    'message' => 'Report Verified Successfully!',
                    'reports' => $all_reports,
                ], 200);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $clientReport = ClientReporting::find($request->id);
        $clientReport->delete();

        // $rem_keyword = K
        return response()->json([
           'message' => 'Report deleted successfully!'
        ], 200);
    }
}
