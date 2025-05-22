@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="detail__content">
    <div class="exhibition-image-area">
        <img class="exhibition-image" src="{{ asset($exhibition->img_url) }}" alt="{{ $exhibition->name }}">
    </div>
    <div class="exhibition-description-area">
        <dl class="exhibition-title">
            <dt class="exhibition-title__name">{{ $exhibition->name }}</dt>
            <dd class="exhibition-title__brand">{{ $exhibition->brand }}</dd>
            <dd class="exhibition-title__price-text"><span class="exhibition-title__price">&yen;{{ $exhibition->price }}</span>（税込）</dd>
        </dl>

        <div class="exhibition-actions">
            <div class="exhibition-actions__item">
                <img class="favorite-icon" src="{{asset('image/星アイコン8.png')}}" alt="いいね">
                <p class="exhibition-actions__count-text">{{$favoriteCount}}</p>
            </div>
            <div class="exhibition-actions__item">
                <img class="comment-icon" src="{{asset('image/ふきだしのアイコン.png')}}" alt="コメント">
                <p class="exhibition-actions__count-text">{{$commentCount}}</p>
            </div>
        </div>

        <form action="/purchase/:{{$exhibition->id}}" class="purchase-form" method="get">
            @csrf
            <button class="purchase-button">購入手続きへ</button>
        </form>

        <dl class="exhibition-description">
            <dt class="exhibition-description__title">商品説明</dt>
            <dd  class="exhibition-description__description">{{$exhibition->description}}</dd>
        </dl>

        <h2 class="exhibition-info__title">商品の情報</h2>
        <dl class="exhibition-info__inner">
            <dt class="exhibition-info__inner-title">カテゴリー</dt>
            <dd class="exhibition-info__categories">
            @foreach($exhibition->categories as $index => $category)
                                <span class="category-tag">{{ $category->category }}</span>
                                @if($index < $exhibition->categories->count() - 1)
                                    <span class="category-separator"></span>
                                @endif
                            @endforeach
            </dd>
            <dt class="exhibition-info__inner-title">商品の状態</dt>
            <dd class="exhibition-info__condition">
            @switch($exhibition->condition_id)
                @case(1)
                    良好
                    @break
                @case(2)
                    目立った傷や汚れなし
                    @break
                @case(3)
                    やや傷や汚れあり
                    @break
                @default
                    状態が悪い
                @endswitch
            </dd>
        </dl>

        <div class="exhibition-comments">
            <h3 class="exhibition-comments__title">コメント&lpar;{{$commentCount}}&rpar;</h3>
            <div class="exhibition-comment__content">
                @foreach ($comments as $comment)
                    <img class="exhibition-comment__user-image" src="{{ $exhibition->img_url }}" alt="{{ $exhibition->name }}">
                    <p class="exhibition-comment__user-name">a</p>
                    <p class="exhibition-comment__comment">{{$comments->comment}}</p>
            @endforeach
            </div>

            <h4>商品へのコメント</h4>
            <form class="comment-form" action="/item/:{{$exhibition->id}}" method="post">
                @csrf
                <div class="comment-form__input--text">
                    <input type="text" class="comment-form__input" name="comment" value="{{ old('comment') }}"/>
                </div>
                <p class="form__error">
                    @error('comment')
                    {{ $message }}
                    @enderror
                </p>

                <div class="comment-form__button">
                    <button class="comment-form__button-submit" type="submit">コメントを送信する</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection