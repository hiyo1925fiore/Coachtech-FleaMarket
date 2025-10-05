<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Profile;
use App\Models\Exhibition;
use App\Models\Purchase;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;

class MypageComponent extends Component
{
    public $activeTab = null; // 初期状態はnull（何も選択されていない）
    public $profile = null;
    public $exhibitions = [];
    public $purchasedExhibitions = [];
    public $tradingExhibitions = [];
    public $unreadCountSum = 0;
    public $unreadCount = [];
    public $user;
    public $averageRating;
    public $ratingCount;


    public function mount()
    {
        $this->user = Auth::user();

        // ユーザーの評価情報を取得
        $this->averageRating = $this->user->averageRating();
        $this->ratingCount = $this->user->ratingCount();

        // URLパラメータからタブを取得（取得可能な値は'sell', 'buy', 'trading'のみ）
        $allowedTabs = ['sell', 'buy', 'trading'];
        $requestedTab = request()->get('page');
        $this->activeTab = in_array($requestedTab, $allowedTabs) ? $requestedTab : null;

        // 初期ロード時にタブが指定されている場合はデータを取得
        if ($this->activeTab) {
            $this->loadTabData();
        }

        // 未読メッセージの合計数を常に計算（バッジ表示用）
        $this->calculateUnreadCountSum();

        // ユーザープロフィールを取得
        $this->profile = Profile::where('user_id', Auth::id())->first();
    }

    private function loadTabData()
    {
        if ($this->activeTab === 'sell') {
            // 出品した商品を取得
            $this->exhibitions = Exhibition::where('seller_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($this->activeTab === 'buy') {
            // 購入した商品を取得
            $purchases = Purchase::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();

            $exhibitionIds = $purchases->pluck('exhibition_id');

            // purchasesの順序を保持して商品を取得
            $this->purchasedExhibitions = [];
            foreach ($purchases as $purchase) {
                $exhibition = Exhibition::find($purchase->exhibition_id);
                if ($exhibition) {
                    $this->purchasedExhibitions[] = $exhibition;
                }
            }
        } elseif ($this->activeTab === 'trading') {
            // 取引中の商品を取得
            $userId = Auth::id();

            // 取引中の全商品を取得
            $allTradingExhibitions = Exhibition::where(function($query) use ($userId) {
                // 出品者または購入者として関わっている商品
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
            ->get();

            // 各商品の最新未読メッセージの日時を取得してソート
            $exhibitionsWithLatestUnread = $allTradingExhibitions->map(function($exhibition) use ($userId) {
                // この商品の未読メッセージの中で最新のもの
                $latestUnreadChat = Chat::where('exhibition_id', $exhibition->id)
                    ->where('receiver_id', $userId)
                    ->whereNull('read_at')
                    ->orderBy('created_at', 'desc')
                    ->first();

                // 未読メッセージがある場合はその日時、ない場合はexhibitionのupdated_at
                // 注：latest_unread_atはソート用の一時プロパティ
                $exhibition->latest_unread_at = $latestUnreadChat
                    ? $latestUnreadChat->created_at
                    : $exhibition->updated_at;

                return $exhibition;
            });

            // latest_unread_atで降順ソート（未読メッセージが新しい順）
            $this->tradingExhibitions = $exhibitionsWithLatestUnread
                ->sortByDesc('latest_unread_at')
                ->values();

            // 各商品の未読数を取得
            foreach ($this->tradingExhibitions as $exhibition) {
                $this->unreadCount[$exhibition->id] = $exhibition->getUnreadCount($userId);
            }
        }
    }

    private function calculateUnreadCountSum()
    {
        $userId = Auth::id();

        // 取引中の商品IDを取得
        $tradingExhibitionIds = Exhibition::where(function($query) use ($userId) {
            // 出品者または購入者として関わっている商品
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
        ->pluck('id');

        // 取引中商品の自分宛て未読メッセージを一括カウント
        $this->unreadCountSum = Chat::where('receiver_id', $userId)
            ->whereIn('exhibition_id', $tradingExhibitionIds)
            ->whereNull('read_at')
            ->count();
    }

    public function render()
    {
        return view('livewire.mypage-component');
    }
}
