<?php

namespace App\Http\Controllers;

use App\Models\BusinessCategory;
use App\Models\Lead;
use App\Models\LeadCloser;
use App\Models\Role;
use App\Models\Sale;
use App\Models\SubCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;


class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $leads = Lead::with('closers') // Eager load the closers relationship directly in the query
        ->orderBy('id', 'DESC')
        ->get();
        // $sale = Sale::where('lead_id', $leads->id)->first();
        return view('pages.lead.index', compact('leads'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = BusinessCategory::all();
        $role = Role::where('name', "Closer")->first();
        $closers = User::where('role_id', $role->id)->get();
        return view('pages.lead.create', compact('categories', 'closers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $formattedCallBackTime = Carbon::parse($request->call_back_time)->format('Y-m-d H:i:s'); // e.g., 2024-10-15 14:30:00
        $request->validate([
            'business_name' => 'required',
            'business_number' => 'required',
            'category' => 'required',

        ]);
      $lead = Lead::create([
            'business_name_adv' => $request->business_name,
            'business_number_adv' => $request->business_number,
            'off_email' => $request->email,
            'client_name' => $request->client_name,
            'client_address' => $request->client_address,
            'client_designation' => $request->client_designation,
            'website_url' => $request->website_url,
            'saler_id' => Auth::user()->id,
            'category_id' => $request->category,
            'lead_status' =>  $request->lead_status,
            'call_status' => $request->call_status,
            'call_back_time' => $formattedCallBackTime,
            'created_by' => Auth::user()->id,
        ]);
        $lead->sub_categories()->attach($request->sub_category);
        // $lead->closers()->attach($request->closers);
        if(isset($request->closers)){
            foreach ($request->closers as $users) {
                LeadCloser::create([
                    'lead_id' => $lead->id,
                    'closer_id' => $users,
                    'created_by' => Auth::user()->id,
                ]);
            }
        }
        Alert::Success('Success', "Lead Genrated Successfully");
        return redirect()->route('lead.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Lead $lead)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $lead = Lead::find($id);
         // dd($lead->closers);
        $sub_categories = $lead->sub_categories;
        $categories = BusinessCategory::all();
        $sub_categories_id = $lead->sub_categories->pluck('id')->toArray();
        $role = Role::where('name', "Closer")->first();
        $closers = User::where('role_id', $role->id)->get();
        // dd($sub_categories_id);
        $related_subcategories = SubCategory::where('business_category_id',  $lead->category_id)
        ->whereNotIn('id', $sub_categories_id)
        ->get();
        // dd($lead->closers);
        return view('pages.lead.edit', compact('lead', 'sub_categories', 'categories','related_subcategories', 'closers' ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $formattedCallBackTime = Carbon::parse($request->call_back_time)->format('Y-m-d H:i:s'); // e.g., 2024-10-15 14:30:00
        $request->validate([
            'business_name' => 'required',
            'business_number' => 'required',
            'category' => 'required',
        ]);
        $lead = Lead::find($id);
        $lead->update([
            'business_name_adv' => $request->business_name,
            'business_number_adv' => $request->business_number,
            'off_email' => $request->email,
            'website_url' => $request->website_url,
            'client_name' => $request->client_name,
            'client_address' => $request->client_address,
            'client_designation' => $request->client_designation,
            'category_id' => $request->category,
            'lead_status' =>  $request->lead_status,
            'call_status' => $request->call_status,
            'call_back_time' => $formattedCallBackTime,
            'updated_by' => Auth::user()->id,
        ]);
        $lead->sub_categories()->sync($request->sub_category);
        // $lead->closers()->sync($request->closers);
        // if(isset($request->closers)){
            $remainingclosers = LeadCloser::where('lead_id', $lead->id)->get();
            if(isset($remainingclosers)){
                foreach ($remainingclosers as $closer) {
                    $closer->delete();
                }
            }
        if(isset($request->closers)){
            foreach ($request->closers as $users) {
                LeadCloser::create([
                    'lead_id' => $lead->id,
                    'closer_id' => $users,
                ]);
            }
        }
        Alert::Success('Success', "Lead Updated Successfully");
        return redirect()->route('lead.index');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $lead = Lead::find($id);
        $lead->delete_by = Auth::user()->id;
        $lead->save();
        $lead->delete();
        Alert::Success('Success', "Lead Deleted Successfully");
        return redirect()->route('lead.index');
    }
}
