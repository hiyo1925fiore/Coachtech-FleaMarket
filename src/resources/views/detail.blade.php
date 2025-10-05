@extends('layouts.app')

@section('head')
<!-- Ajax用のメタタグ -->
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="detail__content">
        <div class="exhibition-image-area">
            <img class="exhibition-image" src="{{ Storage::url($exhibition->img_url) }}" alt="{{ $exhibition->name }}">
        </div>
        <div class="exhibition-description-area">
            <dl class="exhibition-title">
                <dt class="exhibition-title__name">{{ $exhibition->name }}</dt>
                <dd class="exhibition-title__brand">{{ $exhibition->brand }}</dd>
                <dd class="exhibition-title__price">
                    <p class="exhibition-title__price-text">&yen;<span class="exhibition-title__price-price">{{ number_format($exhibition->price) }}</span>&ensp;&lpar;税込&rpar;</p>
                </dd>
            </dl>

            <div class="exhibition-actions">
                <div class="exhibition-actions__item">
                    <button class="favorite-button" data-exhibition-id={{ $exhibition->id }} data-favorited="{{ $isFavorited ? 'true' : 'false' }}">
                        <img class="favorite-icon"
                            src="{{ asset('image/星アイコン8.png') }}"
                            alt="{{ $isFavorited ? 'いいね済み' : 'いいね' }}">
                        <span class="favorite-star {{ $isFavorited ? 'favorited' : '' }}">★</span>
                        <p class="exhibition-actions__count-favorite">{{ $favoriteCount }}</p>
                    </button>
                </div>
                <div class="exhibition-actions__item">
                    <img class="comment-icon" src="{{asset('image/ふきだしのアイコン.png')}}" alt="コメント">
                    <p class="exhibition-actions__count-comment">{{ $comments->count() }}</p>
                </div>
            </div>

            @if($exhibition->isPurchased())
            <p class="purchase-field__sold">
                売り切れ
            </p>
            @else
            <a href="../purchase/:{{$exhibition->id}}" class="purchase-link">
                購入手続きへ
            </a>
            @endif


            <dl class="exhibition-description">
                <dt class="exhibition-description__title">商品説明</dt>
                <dd  class="exhibition-description__description">{{$exhibition->description}}</dd>
            </dl>

            <div class="exhibition-info">
                <h2 class="exhibition-info__title">商品の情報</h2>
                <table class="exhibition-info__inner">
                    <tr class="exhibition-info__row-categories">
                        <th class="exhibition-info__inner-title">カテゴリー</th>
                        <td class="exhibition-info__categories">
                            @foreach($exhibition->categories as $index => $category)
                            <span class="category-tag">{{ $category->category }}</span>
                            @if($index < $exhibition->categories->count() - 1)
                                <span class="category-separator"></span>
                            @endif
                            @endforeach
                        </td>
                    </tr>
                    <tr  class="exhibition-info__row-condition">
                        <th class="exhibition-info__inner-title">商品の状態</th>
                        <td class="exhibition-info__condition">
                            {{ $exhibition->condition->condition }}
                        </td>
                    </tr>
                </table>
            </div>

            <div class="exhibition-comments">
                <h3 class="exhibition-comments__title">コメント&lpar;{{ $comments->count() }}&rpar;</h3>
                <div class="exhibition-comment__content">
                    @foreach ($comments as $comment)
                    <div class="comment-item-user">
                        @if($comment->user->profile && $comment->user->profile->img_url)
                        <img class="comment__user-image"
                            src="{{ Storage::url($comment->user->profile->img_url) }}"
                            alt="{{ $comment->user->name }}">
                        @else
                        <div class="comment__user-image"></div>
                        @endif

                        <p class="comment__user-name">{{ $comment->user->name }}</p>
                    </div>
                    <p class="comment__comment">{{ $comment->comment }}</p>
                    @endforeach
                </div>

                <h4 class="comment-form__title">商品へのコメント</h4>
                <form class="comment-form" action="{{ route('comment.store', $exhibition->id) }}" method="post">
                    @csrf
                    <div class="comment-form__input--text">
                        <textarea class="comment-form__input" name="comment">{{ old('comment') }}</textarea>
                        <p class="form__error">
                            @error('comment')
                            {{ $message }}
                            @enderror
                        </p>
                    </div>
                    <div class="comment-form__button">
                        <button class="comment-form__button-submit" type="submit">コメントを送信する</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScriptファイルを読み込み -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/favorite.js') }}"></script>
@endsection