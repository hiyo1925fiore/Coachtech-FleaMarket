<div  class="container">
    <div class="mypage__info">
        @if($profile->img_url === null)
            <div class="mypage__user-image--none"></div>
        @else
            <img class="mypage__user-image" src="{{ Storage::url($profile->img_url) }}" alt="{{ $user->name }}">
        @endif
        <h2 class="mypage__user-name">{{ $user->name }}</h2>
        <a href="/mypage/profile" class="profile-edit-link">プロフィールを編集</a>
    </div>

    <div class="mypage__tab-nav">
        <div class="tab-buttons">
            <button
                wire:click="selectTab('sell')"
                class="tab-btn {{ $activeTab === 'sell' ? 'active' : '' }}">
                出品した商品
            </button>
            <button
                wire:click="selectTab('buy')"
                class="tab-btn {{ $activeTab === 'buy' ? 'active' : '' }}">
                購入した商品
            </button>
        </div>

        <div class="tab-content">
            @if($activeTab === 'sell')
                <div class="products-grid">
                    @forelse($exhibitions as $exhibition)
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
                    @empty
                        <div class="no-products">
                            <p>出品した商品がありません</p>
                        </div>
                    @endforelse
                </div>
            @elseif($activeTab === 'buy')
                <div class="products-grid">
                    @forelse($purchasedExhibitions as $exhibition)
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
                    @empty
                        <div class="no-products">
                            <p>購入した商品がありません</p>
                        </div>
                    @endforelse
                </div>
            @else
                <div class="no-tab-selected">
                    <p>タブを選択して商品を表示してください</p>
                </div>
            @endif
        </div>
    </div>
</div>
