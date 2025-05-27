<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Exhibition;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ExhibitionController;

class CommentController extends Controller
{
    public function storeComment(CommentRequest $request, $exhibition_id)
    {

        Comment::create([
            'exhibition_id' => $exhibition_id,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);

        return redirect()->route('item.detail', ['exhibition_id' => $exhibition_id]);
    }
}
