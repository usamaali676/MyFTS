<?php

namespace App\Http\Controllers;

use App\Models\Cashapp;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class CashappController extends Controller
{
    public function index()
    {
        $cashapps = Cashapp::orderBy('name')->get();

        return view('pages.settings.cashapp', compact('cashapps'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:cashapps,name,NULL,id,deleted_at,NULL',
        ]);

        Cashapp::create(['name' => $request->name]);

        Alert::success('Success', 'CashApp account added successfully');
        return redirect()->route('cashapp.index');
    }

    public function update(Request $request, $id)
    {
        $cashapp = Cashapp::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:cashapps,name,' . $cashapp->id . ',id,deleted_at,NULL',
        ]);

        $cashapp->update(['name' => $request->name]);

        Alert::success('Success', 'CashApp account updated successfully');
        return redirect()->route('cashapp.index');
    }

    public function destroy($id)
    {
        Cashapp::findOrFail($id)->delete();

        Alert::success('Success', 'CashApp account deleted successfully');
        return redirect()->route('cashapp.index');
    }
}
