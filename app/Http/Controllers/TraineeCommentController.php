<?php

namespace App\Http\Controllers;

use App\Models\Trainee;
use App\Models\TraineeComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class TraineeCommentController extends Controller
{
    public function index($trainee)
    {
        $trainee = Trainee::findOrFail($trainee);
        $comments = $trainee->comments()->with('user')->orderBy('created_at', 'desc')->get();

        return view('pages.trainee-comment.index', compact('trainee', 'comments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'trainee_id' => 'required|exists:trainees,id',
            'comment' => 'required|string',
        ]);

        TraineeComment::create([
            'trainee_id' => $request->trainee_id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
            'comment_date' => now()->toDateString(),
        ]);

        Alert::success('Success', 'Comment Added Successfully');

        return redirect()->route('traineecomment.index', $request->trainee_id);
    }
}
