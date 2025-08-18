<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:255',
            'commentable_type' => 'required|string',
            'commentable_id' => 'required|integer',
        ]);

        $registrationId = session('studentId');

        Comment::create([
            'registration_id'  => $registrationId,
            'content'          => $request->input('content'),
            'model_type' => $request->input('commentable_type'),
            'model_id'   => $request->input('commentable_id'),
        ]);

        return back()->with('success', 'Đã thêm bình luận');
    }

}
