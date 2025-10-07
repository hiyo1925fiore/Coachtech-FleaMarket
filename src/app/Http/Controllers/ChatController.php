<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatRequest;
use App\Models\User;
use App\Models\Exhibition;
use App\Models\Chat;
use App\Models\Rating;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function showChat($exhibitionId){
        // 選択した商品情報を取得
        $exhibition = Exhibition::find($exhibitionId);

        if (!$exhibition) {
            abort(404, '商品が見つかりません');
        }

        // ログインユーザーのIDを取得
        $userId = Auth::id();

        // アクセス制御：出品者または購入者のみアクセス可能
        if ($exhibition->seller_id != $userId &&
            (!$exhibition->purchase || $exhibition->purchase->user_id != $userId)) {
            abort(403, 'この画面にアクセスする権限がありません');
        }

        // 自分が評価済みかチェック
        $myRating = Rating::where('exhibition_id', $exhibitionId)
            ->where('rater_id', $userId)
            ->first();

        // 取引相手の情報を取得
        if ($exhibition->seller_id == $userId) {
            $otherUserId = $exhibition->purchase->user_id;
        } else {
            $otherUserId = $exhibition->seller_id;
        }

        // 相手が評価済みかチェック
        $otherRating = Rating::where('exhibition_id', $exhibitionId)
            ->where('rater_id', $otherUserId)
            ->first();

        // 両者とも評価済みの場合はアクセス不可
        if ($myRating && $otherRating) {
            abort(403, '取引は完了しています');
        }

        // 自分が評価済みで相手が未評価の場合もアクセス不可
        // （自分がやることはもうない）
        if ($myRating && !$otherRating) {
            abort(403, '相手の評価待ちです');
        }

        $otherUser = User::find($otherUserId);

        // 自分宛ての未読メッセージを既読にする
        Chat::where('exhibition_id', $exhibitionId)
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // 取引中の商品を取得（サイドバー表示用）
        $tradingExhibitions = Exhibition::where(function($query) use ($userId) {
                // 出品者または購入者が自分かつ購入済
                $query->where('seller_id', $userId)
                    ->orWhereHas('purchase', function($q) use ($userId) {
                        $q->where('user_id', $userId);
                    });
            })
            ->whereHas('purchase') // 購入済み
            ->whereDoesntHave('ratings', function($query) use ($userId) {
                // 自分がまだ評価していない
                $query->where('rater_id', $userId);
            })
            ->where('id', '!=', $exhibitionId) // 現在表示中の商品を除外
            ->get();

        // チャットを取得（ユーザー情報とプロフィール画像も含む）
        $chats = Chat::where('exhibition_id', $exhibitionId)
            ->with(['user', 'user.profile'])
            ->orderBy('created_at', 'asc')
            ->get();

        // 出品者で、購入者が評価済みかつ自分が未評価の場合、モーダルを自動表示
        $showRatingModal = false;
        if ($exhibition->seller_id == $userId && $otherRating && !$myRating) {
            $showRatingModal = true;
        }

        return view('chat', compact('exhibition', 'userId', 'otherUser', 'tradingExhibitions', 'chats', 'showRatingModal'));
    }

    public function store(ChatRequest $request, $exhibitionId)
    {
        // 選択した商品情報を取得
        $exhibition = Exhibition::find($exhibitionId);

        if (!$exhibition) {
            abort(404, '商品が見つかりません');
        }

        // ログインユーザーのIDを取得
        $id = Auth::id();

        // アクセス制御：出品者または購入者のみメッセージ送信可能
        if ($exhibition->seller_id != $id &&
            (!$exhibition->purchase || $exhibition->purchase->user_id != $id)) {
            abort(403, 'このチャットにメッセージを送信する権限がありません');
        }

        // 自分が評価済みかチェック
        $myRating = Rating::where('exhibition_id', $exhibitionId)
            ->where('rater_id', $id)
            ->first();

        // 評価済みの場合はメッセージ送信不可
        if ($myRating) {
            return redirect()->route('chat.show', $exhibitionId)
                ->with('error', '評価済みのためメッセージを送信できません');
        }

        // 取引相手の情報を取得
        if ($exhibition->seller_id == $id) {
            $otherUser = User::find($exhibition->purchase->user_id);
        } else {
            $otherUser = User::find($exhibition->seller_id);
        }

        //画像をアップロード
        if($request->hasFile('img_url')) {
            $imagePath = $request->file('img_url')->store('img','public');
        }

        Chat::create([
            'exhibition_id' => $exhibitionId,
            'user_id' => $id,
            'receiver_id' =>$otherUser->id,
            'message' => $request->message,
            'img_url' =>$imagePath ?? null,
            'read_at' => null,
        ]);

        return redirect()->route('chat.show', $exhibitionId);
    }
}
