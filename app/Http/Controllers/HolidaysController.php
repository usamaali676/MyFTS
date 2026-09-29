<?php

namespace App\Http\Controllers;

use App\Models\Holidays;
use Carbon\Carbon;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class HolidaysController extends Controller
{
    public function index()
    {
        $holidays = Holidays::orderBy('date')->get();

        return view('pages.settings.holiday', compact('holidays'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        Holidays::create([
            'name' => $request->name,
            'date' => $request->date,
            'day' => Carbon::parse($request->date)->format('l'),
        ]);

        Alert::success('Success', 'Holiday added successfully');
        return redirect()->route('holiday.index');
    }

    public function update(Request $request, $id)
    {
        $holiday = Holidays::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        $holiday->update([
            'name' => $request->name,
            'date' => $request->date,
            'day' => Carbon::parse($request->date)->format('l'),
        ]);

        Alert::success('Success', 'Holiday updated successfully');
        return redirect()->route('holiday.index');
    }

    public function destroy($id)
    {
        // No deleted_at column on this table — hard delete is the only option.
        Holidays::findOrFail($id)->delete();

        Alert::success('Success', 'Holiday deleted successfully');
        return redirect()->route('holiday.index');
    }
}
