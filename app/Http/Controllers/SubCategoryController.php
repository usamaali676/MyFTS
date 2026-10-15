<?php

namespace App\Http\Controllers;

use App\Models\BusinessCategory;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class SubCategoryController extends Controller
{
    public function index()
    {
        $subCategories = SubCategory::with('businessCategory')->orderBy('name')->get();
        $businessCategories = BusinessCategory::orderBy('name')->get();

        return view('pages.settings.sub-category', compact('subCategories', 'businessCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'business_category_id' => 'required|exists:business_categories,id',
            'name' => 'required|string|max:255',
        ]);

        SubCategory::create([
            'business_category_id' => $request->business_category_id,
            'name' => $request->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success', 'Sub category added successfully');
        return redirect()->route('subcategory.index');
    }

    public function update(Request $request, $id)
    {
        $subCategory = SubCategory::findOrFail($id);

        $request->validate([
            'business_category_id' => 'required|exists:business_categories,id',
            'name' => 'required|string|max:255',
        ]);

        $subCategory->update([
            'business_category_id' => $request->business_category_id,
            'name' => $request->name,
            'updated_by' => Auth::user()->id,
        ]);

        Alert::success('Success', 'Sub category updated successfully');
        return redirect()->route('subcategory.index');
    }

    public function destroy($id)
    {
        $subCategory = SubCategory::findOrFail($id);
        $subCategory->deleted_by = Auth::user()->id;
        $subCategory->save();
        $subCategory->delete();

        Alert::success('Success', 'Sub category deleted successfully');
        return redirect()->route('subcategory.index');
    }
}
