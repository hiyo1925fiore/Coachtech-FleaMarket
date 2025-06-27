<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ItemSearchComponent extends Component
{
    public $searchTerm = '';

    // 検索実行時のイベント
    public function search()
    {
        // 検索イベントを発行して、ItemListComponentに検索ワードを伝える
        $this->emit('searchPerformed', $this->searchTerm);
    }

    public function render()
    {
        return view('livewire.item-search-component');
    }
}
