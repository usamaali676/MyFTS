<?php

namespace App\Http\Controllers;

use App\Models\Trainee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class TraineeController extends Controller
{
    public function index()
    {
        // $trainees = Trainee::with('createdBy')->orderBy('id', 'desc')->get();
         $trainees = Trainee::with('createdBy')
        ->orderByRaw("FIELD(status, 'active', 'onBoard', 'suspended', 'inactive')")
        ->orderByDesc('id') // optional: latest first within each status
        ->get();
        $srno = 1;

        return view('pages.trainee.index', compact('trainees', 'srno'));
    }

    public function create()
    {
        return view('pages.trainee.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive,suspended,onBoard',
            'role' => 'required|string|max:255',
            'additional_info' => 'nullable|string',
        ]);

        Trainee::create([
            'name' => $request->name,
            'sudo_name' => $request->sudo_name,
            'role' => $request->role,
            'status' => $request->status,
            'additional_info' => $request->additional_info,
            'created_by_user_id' => Auth::id(),
        ]);

        Alert::success('Success', 'Trainee Added Successfully');

        return redirect()->route('trainee.index');
    }

    public function show($id)
    {
        $trainee = Trainee::findOrFail($id);

        return view('pages.trainee.show', compact('trainee'));
    }

    public function edit($id)
    {
        $trainee = Trainee::findOrFail($id);

        return view('pages.trainee.edit', compact('trainee'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'status' => 'required|in:active,inactive,suspended,onBoard',
            'additional_info' => 'nullable|string',
        ]);

        $trainee = Trainee::findOrFail($id);
        $trainee->sudo_name = $request->sudo_name;
        $trainee->role = $request->role;
        $trainee->status = $request->status;
        $trainee->additional_info = $request->additional_info;
        $trainee->updated_by_user_id = Auth::id();
        $trainee->save();

        Alert::success('Success', 'Trainee Updated Successfully');

        return redirect()->route('trainee.index');
    }

    public function destroy($id)
    {
        $trainee = Trainee::findOrFail($id);
        $trainee->deleted_by_user_id = Auth::id();
        $trainee->save();
        $trainee->delete();

        Alert::success('Success', 'Trainee Deleted Successfully');

        return redirect()->route('trainee.index');
    }
}
