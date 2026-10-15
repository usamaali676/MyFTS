<?php

namespace App\Http\Controllers;

use App\Models\CompanyServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class CompanyServicesController extends Controller
{
    public function index()
    {
        $companyServices = CompanyServices::orderBy('name')->get();

        return view('pages.settings.company-service', compact('companyServices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:company_services,name,NULL,id,deleted_at,NULL',
            'price' => 'required|numeric',
            'category' => 'required|in:Marketing,Development',
        ]);

        CompanyServices::create([
            'name' => $request->name,
            'price' => $request->price,
            'category' => $request->category,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success', 'Company service added successfully');
        return redirect()->route('companyservice.index');
    }

    public function update(Request $request, $id)
    {
        $companyService = CompanyServices::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:company_services,name,' . $companyService->id . ',id,deleted_at,NULL',
            'price' => 'required|numeric',
            'category' => 'required|in:Marketing,Development',
        ]);

        $companyService->update([
            'name' => $request->name,
            'price' => $request->price,
            'category' => $request->category,
            'updated_by' => Auth::user()->id,
        ]);

        Alert::success('Success', 'Company service updated successfully');
        return redirect()->route('companyservice.index');
    }

    public function destroy($id)
    {
        $companyService = CompanyServices::findOrFail($id);
        $companyService->deleted_by = Auth::user()->id;
        $companyService->save();
        $companyService->delete();

        Alert::success('Success', 'Company service deleted successfully');
        return redirect()->route('companyservice.index');
    }
}
