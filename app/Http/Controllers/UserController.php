<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
{
    //
    public function index() {
        $user = User::orderBy('id', 'DESC')->get();
        $srno = 1;
        return view('pages.user.index', compact('user', 'srno'));
    }
    public function create() {
        $role = Role::all();
        return view('pages.user.create', compact('role'));
    }
    public function store(Request $request) {

        $request->validate([
            'role_id' => 'required',
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);
        // dd($request->all());
        User::create([
            'role_id' => $request->role_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'created_by' => Auth::user()->id,
        ]);
        Alert::success('Succes', "User Added Successfully");
        return redirect()->route('user.index');
    }
    public function edit($id) {
        $user = User::find($id);
        $role = Role::all();
        // dd($role);
        return view('pages.user.edit', compact('user', 'role'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'role_id' => 'required',
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
        ]);
        $user = User::find($id);
        $user->role_id = $request->role_id;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->updated_by = Auth::user()->id;
        if($request->password!=null){
            $user->password = bcrypt($request->password);
        }
        $user->save();
        Alert::success('Success', "User Updated Successfully");
        return redirect()->route('user.index');
    }

    public function destroy($id) {
        $user = User::find($id);
        $user->deleted_by = Auth::user()->id;
        $user->save();
        $user->delete();
        Alert::success('Success', "User Deleted Successfully");
        return redirect()->route('user.index');
    }
}
