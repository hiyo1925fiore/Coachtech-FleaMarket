<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Condition;
use App\Models\Exhibition;
use App\Models\Favorite;

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
        $favorites = Favorite::where('exhibition_id', $exhibition_id)->get();
        $conditions = Condition::all();
        $comments = Comment::where('exhibition_id', $exhibition_id)->latest()->get();
        $favoriteCount = Favorite::where('exhibition_id', $exhibition_id)->count();
        $commentCount = Comment::where('exhibition_id', $exhibition_id)->count();

        return view('detail', compact('exhibition', 'favorites', 'conditions', 'comments', 'favoriteCount', 'commentCount'));
    }
}
