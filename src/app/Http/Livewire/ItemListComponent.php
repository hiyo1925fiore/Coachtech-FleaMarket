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
    public $searchTerm = '';

    // Livewireリスナーを登録 - 検索イベントを監視
    protected $listeners = ['searchPerformed' => 'updateSearchTerm'];

    public function mount()
    {
        // URLパラメータから初期タブを設定
        $this->activeTab = request()->input('page') === 'mylist' ? 'mylist' : 'recommended';

        $this->loadRecommendedExhibitions();
        $this->loadMyListExhibitions();
    }

    // 検索語が変更された時に呼ばれるメソッド
    public function updateSearchTerm($term)
    {
        $this->searchTerm = $term;
        $this->loadRecommendedExhibitions();
        $this->loadMyListExhibitions();
    }

    // エンターキーが押されたときに実行される検索メソッド
    public function performSearch()
    {
        $this->loadRecommendedExhibitions();
        $this->loadMyListExhibitions();
    }

    public function loadRecommendedExhibitions()
    {
        // おすすめ商品の取得ロジック
        $id=Auth::id();

        $query = Exhibition::where('seller_id', '<>', $id)
            ->with(['purchase']);

        // 検索ワードが入力されている場合のみ検索結果を反映する
        if (!empty($this->searchTerm)) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        }

        $this->recommendedExhibitions = $query->latest()->get();
    }

    public function loadMyListExhibitions()
    {
        // マイリスト商品の取得ロジック
        $id=Auth::id();

        $favoriteExhibitionIds = Favorite::where('user_id', $id)
            ->pluck('exhibition_id')
            ->toArray();

        $query = Exhibition::whereIn('id', $favoriteExhibitionIds)
            ->with(['purchase']);

        // 検索ワードが入力されている場合のみ検索結果を反映する
        if (!empty($this->searchTerm)) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        }

        $this->myListExhibitions = $query->latest()->get();

        //$this->myListExhibitions = Exhibition::whereIn('id', $favoriteExhibitionIds)
        //->with(['purchase'])
            //->latest()
            //->get();
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