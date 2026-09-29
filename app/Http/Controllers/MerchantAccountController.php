<?php

namespace App\Http\Controllers;

use App\Models\MerchantAccount;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class MerchantAccountController extends Controller
{
    public function index()
    {
        $merchantAccounts = MerchantAccount::orderBy('name')->get();

        return view('pages.settings.merchant-account', compact('merchantAccounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:merchant_accounts,name,NULL,id,deleted_at,NULL',
        ]);

        MerchantAccount::create(['name' => $request->name]);

        Alert::success('Success', 'Merchant account added successfully');
        return redirect()->route('merchantaccount.index');
    }

    public function update(Request $request, $id)
    {
        $merchantAccount = MerchantAccount::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:merchant_accounts,name,' . $merchantAccount->id . ',id,deleted_at,NULL',
        ]);

        $merchantAccount->update(['name' => $request->name]);

        Alert::success('Success', 'Merchant account updated successfully');
        return redirect()->route('merchantaccount.index');
    }

    public function destroy($id)
    {
        MerchantAccount::findOrFail($id)->delete();

        Alert::success('Success', 'Merchant account deleted successfully');
        return redirect()->route('merchantaccount.index');
    }
}
