<?php

namespace App\Http\Controllers;

use App\Models\BusinessCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class BusinessCategoryController extends Controller
{
    public function index()
    {
        $businessCategories = BusinessCategory::orderBy('name')->get();

        return view('pages.settings.business-category', compact('businessCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:business_categories,name,NULL,id,deleted_at,NULL',
        ]);

        BusinessCategory::create([
            'name' => $request->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success', 'Business category added successfully');
        return redirect()->route('businesscategory.index');
    }

    public function update(Request $request, $id)
    {
        $businessCategory = BusinessCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:business_categories,name,' . $businessCategory->id . ',id,deleted_at,NULL',
        ]);

        $businessCategory->update([
            'name' => $request->name,
            'updated_by' => Auth::user()->id,
        ]);

        Alert::success('Success', 'Business category updated successfully');
        return redirect()->route('businesscategory.index');
    }

    public function destroy($id)
    {
        $businessCategory = BusinessCategory::findOrFail($id);
        $businessCategory->deleted_by = Auth::user()->id;
        $businessCategory->save();
        $businessCategory->delete();

        Alert::success('Success', 'Business category deleted successfully');
        return redirect()->route('businesscategory.index');
    }
}
