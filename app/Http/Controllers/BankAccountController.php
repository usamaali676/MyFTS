<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class BankAccountController extends Controller
{
    public function index()
    {
        $bankAccounts = BankAccount::orderBy('name')->get();

        return view('pages.settings.bank-account', compact('bankAccounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:bank_accounts,name,NULL,id,deleted_at,NULL',
        ]);

        BankAccount::create(['name' => $request->name]);

        Alert::success('Success', 'Bank account added successfully');
        return redirect()->route('bankaccount.index');
    }

    public function update(Request $request, $id)
    {
        $bankAccount = BankAccount::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:bank_accounts,name,' . $bankAccount->id . ',id,deleted_at,NULL',
        ]);

        $bankAccount->update(['name' => $request->name]);

        Alert::success('Success', 'Bank account updated successfully');
        return redirect()->route('bankaccount.index');
    }

    public function destroy($id)
    {
        BankAccount::findOrFail($id)->delete();

        Alert::success('Success', 'Bank account deleted successfully');
        return redirect()->route('bankaccount.index');
    }
}
