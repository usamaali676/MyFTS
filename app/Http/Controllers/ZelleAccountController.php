<?php

namespace App\Http\Controllers;

use App\Models\ZelleAccount;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class ZelleAccountController extends Controller
{
    public function index()
    {
        $zelleAccounts = ZelleAccount::orderBy('name')->get();

        return view('pages.settings.zelle-account', compact('zelleAccounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:zelle_accounts,name,NULL,id,deleted_at,NULL',
        ]);

        ZelleAccount::create(['name' => $request->name]);

        Alert::success('Success', 'Zelle account added successfully');
        return redirect()->route('zelleaccount.index');
    }

    public function update(Request $request, $id)
    {
        $zelleAccount = ZelleAccount::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:zelle_accounts,name,' . $zelleAccount->id . ',id,deleted_at,NULL',
        ]);

        $zelleAccount->update(['name' => $request->name]);

        Alert::success('Success', 'Zelle account updated successfully');
        return redirect()->route('zelleaccount.index');
    }

    public function destroy($id)
    {
        ZelleAccount::findOrFail($id)->delete();

        Alert::success('Success', 'Zelle account deleted successfully');
        return redirect()->route('zelleaccount.index');
    }
}
