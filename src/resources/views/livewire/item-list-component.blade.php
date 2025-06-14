<div class="itemlist__content">
    {{-- resources/views/livewire/itemlist-component.blade.php --}}
    <div class="itemlist__tab-nav">
        <a
            href="/"
            wire:navigate
            class="{{ $activeTab === 'recommended' ? 'nav-item--active' : 'nav-item' }}"
        >
            おすすめ
        </a>
        <a
            href="/?page=mylist"
            wire:navigate
            class="{{ $activeTab === 'mylist' ? 'nav-item--active' : 'nav-item' }}"
        >
            マイリスト
        </a>
    </div>

    <div class="item-list">
        @if(count($exhibitions) > 0)
            <div class="items-grid">
                @foreach ($exhibitions as $exhibition)
                    <div class="item-card">
                        <a href="/item/:{{$exhibition->id}}" class="detail-link">
                            <div class="item-image">
                                <img class="item-card__inner--image" src="{{ Storage::url($exhibition->img_url) }}" alt="{{ $exhibition->name }}">

                                @if($exhibition->isPurchased())
                                <div class="item-card__inner--sold">
                                    <div class="item-card__sold--text">Sold</div>
                                </div>
                                @endif
                            </div>
                            <p class="item-card__inner--name">{{ $exhibition->name }}</p>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="no-results">
                @if(!empty($searchTerm))
                    <p>「{{ $searchTerm }}」に一致する商品が見つかりませんでした。</p>
                @else
                    <p>表示する商品がありません</p>
                @endif
            </div>
        @endif
    </div>
</div>
