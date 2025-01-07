<?php

namespace App\Http\Controllers;

use App\Models\Comments;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'stage' => 'required',
                'comment' => 'required',
                'lead_id' => 'required',
            ]);
            $comment = Comments::create([
                'Stage' => $request->stage,
                'comment' => $request->comment,
                'due_date' => $request->due_date,
                'lead_id' => $request->lead_id,
                'user_id' => Auth::user()->id,
            ]);
            $comments = Comments::where('lead_id', $comment->lead_id)->orderby('id', 'DESC')->with('user')->get();
            return response()->json([
                'message' => 'Comment Added Successfully!',
                'comments' => $comments,
            ], 200);
        } catch (Exception $e) {
            // Log the exception for debugging
            Log::error($e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Comments $comments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comments $comments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comments $comments)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comments $comments)
    {
        //
    }
}
