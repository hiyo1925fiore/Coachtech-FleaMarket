<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Condition;
use App\Models\Exhibition;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class ExhibitionController extends Controller
{
    public function getList(){
        return view('itemlist');
    }

    public function getDetail($exhibitionId){
        $exhibition = Exhibition::with('categories')->find($exhibitionId);

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
        $comments = Comment::where('exhibition_id', $exhibitionId)
            ->with(['user', 'user.profile'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('detail', compact('exhibition', 'favoriteCount', 'isFavorited', 'comments'));
    }

    public function getExhibition()
    {
        $categories = Category::all();
        $conditions = Condition::all();

        return view('exhibition', compact('categories', 'conditions'));
    }

    public function storeExhibition(ExhibitionRequest $request)
    {
        //画像をアップロード
        if($request->hasFile('img_url')) {
            $imagePath = $request->file('img_url')->store('img','public');
        }

        $id = Auth::id();

        //exhibitionsテーブルに格納
        $exhibition = Exhibition::create([
            'seller_id' => $id,
            'condition_id' => $request->condition_id,
            'name' => $request->name,
            'brand' => $request->brand,
            'price' => $request->price,
            'img_url' =>$imagePath ?? null,
            'description' => $request->description,
        ]);

        //category_idを中間テーブルに格納
        $exhibition->categories()->attach($request->category_id);

        return redirect('/');
    }
}
