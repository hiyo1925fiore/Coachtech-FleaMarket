<div>
    {{-- resources/views/livewire/itemlist-component.blade.php --}}
    <div class="itemlist__tab-nav">
        <a
            href="/"
            wire:navigate
            class="{{ $activeTab === 'recommended' ? 'active' : '' }}"
        >
            おすすめ
        </a>
        <a
            href="/?page=mylist"
            wire:navigate
            class="{{ $activeTab === 'mylist' ? 'active' : '' }}"
        >
            マイリスト
        </a>
    </div>

    <div class="item-list">
        @forelse ($exhibitions as $exhibition)
            <div class="item-card">
                <img class="item-card__inner--image" src="{{ $exhibition->img_url }}" alt="{{ $exhibition->name }}">
                <div class="item-card__inner--sold"><p>sold</p></div>
                <p class="item-card__inner--name">{{ $exhibition->name }}</p>
            </div>
        @empty
            <p>表示する商品がありません</p>
        @endforelse
    </div>
</div>
