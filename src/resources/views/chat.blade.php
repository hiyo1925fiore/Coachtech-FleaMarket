@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="sidebar">
        <h3 class="sidebar__title">その他の取引</h3>
        <ul class="sidebar__trading-list">
            <li class="sidebar__trading-list-item"><a class="sidebar__link" href="#">{{ $exhibition->name }}</a></li>
        </ul>
    </div>

    <div class="chat__content">
        <div class="title">
            <div class="title__inner">
                @if($otherUser->profile->img_url === "")
                <div class="other-user-image--none"></div>
                @else
                <img class="other-user-image" src="{{ Storage::url($otherUser->profile->img_url) }}" alt="{{ $otherUser->name }}">
                @endif
                <h2 class="other-user-name">{{ $otherUser->name }}さんとの取引画面</h2>
            </div>
            @if($userId != $exhibition->seller_id)
            <a class="trade-close-button" href="#{{$exhibition->id}}">取引を完了する</a>
            @endif
        </div>

        <div class="exhibition-info">
            <div class="exhibition-image-area">
                <img class="exhibition-image" src="{{ Storage::url($exhibition->img_url) }}" alt="{{ $exhibition->name }}">
            </div>
            <dl class="exhibition-info__item">
                <dt class="exhibition-info__name">{{ $exhibition->name }}</dt>
                <dd class="exhibition-info__price">
                    <p class="exhibition-info__price-text">&yen;<span class="exhibition-info__price-price">{{ number_format($exhibition->price) }}</span>&ensp;&lpar;税込&rpar;</p>
                </dd>
            </dl>
        </div>

        <div class="chat-area">
            @foreach ($chats as $chat)
            @if($chat->user_id == $userId)
            <div class="chat__item--own">
                <div class="chat__own-user-info">
                    <p class="chat__sender-name">{{ $chat->user->name }}</p>
                    @if($chat->user->profile && $chat->user->profile->img_url)
                    <img class="chat__sender-image"
                        src="{{ Storage::url($chat->user->profile->img_url) }}"
                        alt="{{ $chat->user->name }}">
                    @else
                    <div class="chat__sender-image"></div>
                    @endif
                </div>
                <p class="chat__message">{{ $chat->message }}</p>
                @if($chat->img_url != '')
                <img class="chat__image"
                    src="{{ Storage::url($chat->img_url) }}"
                    alt="">
                @endif
                <div class="chat__edit-tool">
                    <p>編集</p>
                    <p>削除</p>
                </div>
            </div>
            @else
            <div class="chat__item--other-user">
                <div class="chat__other-user-info">
                    @if($chat->user->profile && $chat->user->profile->img_url)
                    <img class="chat__sender-image"
                        src="{{ Storage::url($chat->user->profile->img_url) }}"
                        alt="{{ $chat->user->name }}">
                    @else
                    <div class="chat__sender-image"></div>
                    @endif
                    <p class="chat__sender-name">{{ $chat->user->name }}</p>
                </div>
                <p class="chat__message">{{ $chat->message }}</p>
                @if($chat->img_url != '')
                <img class="chat__image"
                    src="{{ Storage::url($chat->img_url) }}"
                    alt="">
                @endif
            </div>
            @endif
            @endforeach
        </div>
        <div class="chat-input-area">
            <div class="chat-form_image-preview-container" id="image-preview-container">
            </div>
            <div class="chat-input__inner">
                <p class="form__error">
                    @error('message')
                    {{ $message }}
                    @enderror
                </p>
                <p class="form__error">
                    @error('img_url')
                    {{ $message }}
                    @enderror
                </p>
                <form class="chat-form" action="{{ route('chat.store', $exhibition->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <textarea class="chat-form__text-area" name="message" placeholder="取引メッセージを記入してください">{{ old('message') }}</textarea>
                    <div class="chat-form__file-input-wrapper">
                        <input class="chat-form__file" type="file" name="img_url" id="img_url" style="display: none;" accept="image/*">
                        <label for="img_url" class="chat-form__file-select-button">画像を追加</label>
                    </div>
                    <div class="chat-form__button">
                        <button class="chat-form__button-submit" type="submit">
                            <img class="chat-form__button-icon" src="{{ asset('image/チャット送信ボタン.png') }}" alt="送信">
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- 選択した画像のプレビュー＆画像選択時のみ画像名を表示する -->
<script src="{{ asset('js/exhibition_preview_image.js') }}"></script>
@endsection