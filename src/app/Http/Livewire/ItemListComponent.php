<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Exhibition;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ItemListComponent extends Component
{
    public $activeTab = 'recommended';
    public $recommendedExhibitions = [];
    public $myListExhibitions = [];

    public function mount()
    {
        // URLパラメータから初期タブを設定
        $this->activeTab = request()->input('page') === 'mylist' ? 'mylist' : 'recommended';

        $this->loadRecommendedExhibitions();
        $this->loadMyListExhibitions();
    }

    public function loadRecommendedExhibitions()
    {
        // おすすめ商品の取得ロジック
        $id=Auth::id();

        $this->recommendedExhibitions = Exhibition::where('seller_id', '<>', $id)
            ->latest()
            ->get();
    }

    public function loadMyListExhibitions()
    {
        // マイリスト商品の取得ロジック
        $id=Auth::id();

        $favoriteExhibitionIds = Favorite::where('user_id', $id)
            ->pluck('exhibition_id')
            ->toArray();

        $this->myListExhibitions = Exhibition::whereIn('id', $favoriteExhibitionIds)
            ->latest()
            ->get();
    }

    public function changeTab($tab)
    {
        $this->activeTab = $tab;

        // URLを動的に更新
        if ($tab === 'recommended') {
            return redirect('/');
        } else {
            return redirect('/?page=mylist');
        }
    }

    public function render()
    {
        return view('livewire.item-list-component', [
            'exhibitions' => $this->activeTab === 'recommended' ? $this->recommendedExhibitions : $this->myListExhibitions
        ]);
    }
}
