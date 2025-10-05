<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatRequest;
use App\Models\User;
use App\Models\Exhibition;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function showChat($exhibitionId){
        // 選択した商品情報を取得
        $exhibition = Exhibition::find($exhibitionId);

        // ログインユーザーのIDを取得
        $userId = Auth::id();

        // 自分宛ての未読メッセージを既読にする
        Chat::where('exhibition_id', $exhibitionId)
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // 取引相手の情報を取得
        if ($exhibition->seller_id == $userId) {
            $otherUser = User::find($exhibition->purchase->user_id);
        } else {
            $otherUser = User::find($exhibition->seller_id);
        }

        // 取引中の商品を取得（サイドバー表示用）

        // チャットを取得（ユーザー情報とプロフィール画像も含む）
        $chats = Chat::where('exhibition_id', $exhibitionId)
            ->with(['user', 'user.profile'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('chat', compact('exhibition', 'userId', 'otherUser', 'chats'));
    }

    public function storeChat(ChatRequest $request, $exhibitionId)
    {
        // 選択した商品情報を取得
        $exhibition = Exhibition::find($exhibitionId);

        // ログインユーザーのIDを取得
        $id = Auth::id();

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
