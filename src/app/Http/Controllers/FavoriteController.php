<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Exhibition;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggleFavorite(Request $request, $exhibition_id)
    {
        $exhibition = Exhibition::with('categories')->find($exhibition_id);

        $user = Auth::user();

        // 既にいいねしているかチェック
        $favorite = Favorite::where('user_id', $user->id)
            ->where('exhibition_id', $exhibition_id)
            ->first();

        if ($favorite) {
            // いいねを削除
            $favorite->delete();
            $isFavorited = false;
        } else {
            // いいねを追加
            Favorite::create([
                'user_id' => $user->id,
                'exhibition_id' => $exhibition_id
            ]);
            $isFavorited = true;
        }

        // 新しいいいね数を取得
        $favoriteCount = $exhibition->favorites()->count();

        return response()->json([
            'isFavorited' => $isFavorited,
            'favoriteCount' => $favoriteCount
        ]);
    }
}
