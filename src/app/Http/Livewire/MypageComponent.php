<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Profile;
use App\Models\Exhibition;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;

class MypageComponent extends Component
{
    public $activeTab = null; // 初期状態はnull（何も選択されていない）
    public $profile = null;
    public $exhibitions = [];
    public $purchasedExhibitions = [];
    public $user;

    public function mount()
    {
        $this->user = Auth::user();

        // URLパラメータからタブを取得（token等の不要パラメータは無視）
        $allowedTabs = ['sell', 'buy'];
        $requestedTab = request()->get('tab');
        $this->activeTab = in_array($requestedTab, $allowedTabs) ? $requestedTab : null;
        
        // ユーザープロフィールを取得
        $this->profile = Profile::where('user_id', Auth::id())->first();
        
        // 初期ロード時にタブが指定されている場合はデータを取得
        if ($this->activeTab) {
            $this->loadTabData();
        }
        
        // 初回アクセス時にURLをクリーンアップ
        if (!$this->activeTab) {
            $this->emit('cleanUrl');
        }
    }

    public function selectTab($tab)
    {
        $this->activeTab = $tab;
        $this->loadTabData();
        
        // URLを更新（ページリロードせずに）
        $this->emit('updateUrl', $tab);
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
        }
    }

    public function render()
    {
        return view('livewire.mypage-component');
    }
}
