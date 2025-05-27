<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Exhibition;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class ExhibitionController extends Controller
{
    public function getList(){
        return view('itemlist');
    }

    public function getExhibition(){
        return view('exhibition');
    }

    public function getDetail($exhibition_id){
        $exhibition = Exhibition::with('categories')->find($exhibition_id);

        // いいね数を取得
        $favoriteCount = $exhibition->favorites()->count();

        // 現在のユーザーがいいねしているかチェック
        $isFavorited = false;
        if (Auth::check()) {
            $isFavorited = $exhibition->favorites()
                ->where('user_id', Auth::id())
                ->exists();
        }

        // コメントを取得（ユーザー情報とプロフィール画像も含む）
        $comments = Comment::where('exhibition_id', $exhibition_id)
            ->with(['user', 'user.profile'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('detail', compact('exhibition', 'favoriteCount', 'isFavorited', 'comments'));
    }
}
