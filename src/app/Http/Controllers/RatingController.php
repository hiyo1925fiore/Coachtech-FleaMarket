<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Exhibition;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TradeCompleteNotification;

class RatingController extends Controller
{
    public function store(Request $request, $exhibitionId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $exhibition = Exhibition::with('purchase')->findOrFail($exhibitionId);
        $userId = Auth::id();

        // アクセス制御：出品者または購入者のみ評価可能
        if ($exhibition->seller_id != $userId &&
            (!$exhibition->purchase || $exhibition->purchase->user_id != $userId)) {
            abort(403, '評価する権限がありません');
        }

        // すでに評価済みかチェック
        $existingRating = Rating::where('exhibition_id', $exhibitionId)
            ->where('rater_id', $userId)
            ->first();

        if ($existingRating) {
            return redirect()->route('itemlist',['page' => 'mylist'])
                ->with('error', 'すでに評価済みです。');
        }

        // 取引相手のIDを取得
        if ($exhibition->seller_id == $userId) {
            // 出品者の場合、購入者を評価
            $ratedUserId = $exhibition->purchase->user_id;
        } else {
            // 購入者の場合、出品者を評価
            $ratedUserId = $exhibition->seller_id;
        }

        // 評価を保存（user_idカラムに評価される人のIDを格納）
        Rating::create([
            'exhibition_id' => $exhibitionId,
            'rater_id' => $userId,
            'user_id' => $ratedUserId,
            'rating' => $request->rating,
        ]);

        // 購入者が評価した場合のみ、出品者に通知メールを送信
        if ($exhibition->seller_id != $userId) {
            $seller = User::find($exhibition->seller_id);
            Mail::to($seller->email)->send(new TradeCompleteNotification($exhibition, $userId));
        }

        return redirect()->route('itemlist',['page' => 'mylist'])
            ->with('success', '評価を送信しました。');
    }
}
