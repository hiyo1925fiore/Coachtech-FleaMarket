<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function storeComment(CommentRequest $request, $exhibitionId)
    {

        Comment::create([
            'exhibition_id' => $exhibitionId,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);

        return redirect()->route('item.detail', ['exhibition_id' => $exhibitionId]);
    }
}
