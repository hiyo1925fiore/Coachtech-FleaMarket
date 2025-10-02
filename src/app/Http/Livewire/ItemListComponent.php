<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Exhibition;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

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

        // URLから検索ワードを取得（ページリロード時に保持）
        $this->searchTerm = request()->input('searchTerm', '');

        $this->loadExhibitions();
    }

    // 検索語が変更された時に呼ばれるメソッド
    public function updateSearchTerm($term)
    {
        $this->searchTerm = $term;
        $this->loadExhibitions();

        // 検索後にURLを更新
        $this->updateUrl();
    }

    // おすすめ商品とマイリスト商品をロードする
    public function loadExhibitions()
    {
        $this->loadRecommendedExhibitions();
        $this->loadMyListExhibitions();
    }

    public function loadRecommendedExhibitions()
    {
        // おすすめ商品の取得ロジック
        $query = Exhibition::where('seller_id', '<>', Auth::id())
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
        $favoriteExhibitionIds = Favorite::where('user_id', Auth::id())
            ->pluck('exhibition_id')
            ->toArray();

        $query = Exhibition::whereIn('id', $favoriteExhibitionIds)
            ->with(['purchase']);

        // 検索ワードが入力されている場合のみ検索結果を反映する
        if (!empty($this->searchTerm)) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        }

        $this->myListExhibitions = $query->latest()->get();
    }

    public function changeTab($tab)
    {
        $this->activeTab = $tab;

        // URLを更新
        $this->updateUrl();
    }

    // URLを更新するメソッド
    private function updateUrl()
    {
        $params = [];

        if ($this->activeTab === 'mylist') {
            $params['page'] = 'mylist';
        }

        if (!empty($this->searchTerm)) {
            $params['searchTerm'] = $this->searchTerm;
        }

        $url = empty($params) ? '/' : '/?' . http_build_query($params);

        // JavaScriptを使ってURLを更新（ページリロードなし）
        $this->dispatchBrowserEvent('update-url', ['url' => $url]);
    }

    public function render()
    {
        return view('livewire.item-list-component', [
            'exhibitions' => $this->activeTab === 'recommended' ? $this->recommendedExhibitions : $this->myListExhibitions
        ]);
    }
}