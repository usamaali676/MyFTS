<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function get_subcategory(Request $request){
        if($request->ajax()){
            $sub_category = SubCategory::where('business_category_id' , $request->selected)->get();
            return response()->json(['sub_category' => $sub_category]);
        }
    }
}
