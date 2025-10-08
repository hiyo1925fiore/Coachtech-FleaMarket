@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="sidebar">
        <h3 class="sidebar__title">その他の取引</h3>
        <ul class="sidebar__trading-list">
            @foreach($tradingExhibitions as $tradingExhibition)
            <li class="sidebar__trading-list-item">
                <a class="sidebar__link" href="{{ route('chat.show', $tradingExhibition->id) }}">
                    {{ $tradingExhibition->name }}
                </a>
            </li>
            @endforeach
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
            <a class="trade-close-button" href="javascript:void(0);">取引を完了する</a>
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
                <p class="chat__message" id="message-{{ $chat->id }}">{{ $chat->message }}</p>
                @if($chat->img_url != '')
                <img class="chat__image"
                    src="{{ Storage::url($chat->img_url) }}"
                    alt="">
                @endif
                <div class="chat__edit-tool">
                    <button class="chat__edit-button" data-chat-id="{{ $chat->id }}" data-message="{{ $chat->message }}">編集</button>
                    <form class="chat__delete-form" action="{{ route('chat.destroy', $chat->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="chat__delete-button" type="submit" onclick="return confirm('このメッセージを削除しますか？')">削除</button>
                    </form>
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
                <!-- 新規メッセージ送信時のエラーのみ表示 -->
                @if($errors->any() && !session('is_edit_mode'))
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
                @endif
                <form class="chat-form" action="{{ route('chat.store', $exhibition->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <textarea class="chat-form__text-area" name="message" placeholder="取引メッセージを記入してください">{{ !session('is_edit_mode') ? old('message') : '' }}</textarea>
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

        <!-- 評価モーダル -->
        <div class="modal">
            <div class="modal-overlay"></div>
            <div class="modal__inner">
                <div class="modal__content">
                    <form class="modal__rating-submit-form" action="{{ route('rating.store', $exhibition->id) }}" method="post">
                        @csrf
                        <div class="modal-form__title">
                            <h4 class="modal-form__title-text">取引が完了しました。</h4>
                        </div>
                        <div class="modal-form__inner">
                            <p class="modal-form__question">今回の取引相手はどうでしたか？</p>
                            <div class="modal-form__stars">
                                <span class="star" data-value="1">★</span>
                                <span class="star" data-value="2">★</span>
                                <span class="star" data-value="3">★</span>
                                <span class="star" data-value="4">★</span>
                                <span class="star" data-value="5">★</span>
                            </div>
                        </div>
                        <div class="modal-form__submit">
                            <input type="hidden" name="rating" value="3">
                            <button class="modal-form__rating-submit-button" type="submit">送信する</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- 編集モーダル -->
        <div class="edit-modal" id="edit-modal">
            <div class="edit-modal-overlay"></div>
            <div class="edit-modal__inner">
                <div class="edit-modal__content">
                    <h4 class="edit-modal__title">メッセージを編集</h4>
                    <form class="edit-modal__form" id="edit-form" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="edit-modal__error" id="edit-error">
                            <!-- エラーメッセージがここに表示される -->
                        </div>
                        <textarea class="edit-modal__textarea" name="message" id="edit-message"></textarea>
                        <div class="edit-modal__buttons">
                            <button class="edit-modal__cancel" type="button" id="edit-cancel">キャンセル</button>
                            <button class="edit-modal__submit" type="submit">更新</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- 選択した画像のプレビューを表示する -->
<script src="{{ asset('js/preview_image.js') }}"></script>
<!-- 画像削除機能（チャット画面専用） -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const imageInput = document.getElementById("img_url");
    const previewContainer = document.getElementById("image-preview-container");
    const fileSelectButton = document.querySelector(".chat-form__file-select-button");

    // 画像選択時の処理を拡張
    imageInput.addEventListener("change", function () {
        if (this.files && this.files.length > 0) {
            // 削除ボタンを追加
            addDeleteButton();
            // 画像追加ボタンを無効化
            disableFileSelectButton();
        }
    });

    // 削除ボタンを追加する関数
    function addDeleteButton() {
        // 既存の削除ボタンがあれば削除
        const existingButton = previewContainer.querySelector(".image-delete-button");
        if (existingButton) {
            existingButton.remove();
        }

        // 新しい削除ボタンを作成
        const deleteButton = document.createElement("button");
        deleteButton.classList.add("image-delete-button");
        deleteButton.type = "button";
        deleteButton.setAttribute("aria-label", "画像を削除");

        // 削除ボタンのクリックイベント
        deleteButton.addEventListener("click", function () {
            // 画像選択をクリア
            imageInput.value = "";
            // プレビューを削除
            previewContainer.innerHTML = "";
            // 画像追加ボタンを有効化
            enableFileSelectButton();
        });

        previewContainer.appendChild(deleteButton);
    }

    // 画像追加ボタンを無効化
    function disableFileSelectButton() {
        fileSelectButton.classList.add("disabled");
    }

    // 画像追加ボタンを有効化
    function enableFileSelectButton() {
        fileSelectButton.classList.remove("disabled");
    }
});
</script>
<!-- チャット入力欄の内容を保持する -->
<script src="{{ asset('js/chat_save_message.js') }}"></script>
<!-- メッセージを編集する -->
<script src="{{ asset('js/edit_message.js') }}"></script>
<!-- 取引完了処理 -->
<script src="{{ asset('js/trade_complete.js') }}"></script>
@if($showRatingModal)
<script>
    // 出品者で購入者が評価済みの場合、ページ読み込み時にモーダルを表示
    window.addEventListener('DOMContentLoaded', function() {
        const modal = document.querySelector('.modal');
        const modalOverlay = modal.querySelector('.modal-overlay');

        modal.style.visibility = 'visible';
        modal.style.opacity = '1';

        if (modalOverlay) {
            modalOverlay.style.pointerEvents = 'none';
        }
    });
</script>
@endif
@if(session('is_edit_mode') && $errors->any())
<script>
    // バリデーションエラーがある場合、編集モーダルを再表示
    window.addEventListener('DOMContentLoaded', function() {
        const chatId = "{{ session('edit_chat_id') }}";
        const message = "{{ old('message', '') }}";
        const errors = @json($errors->all());

        if (chatId && typeof window.openEditModalWithError === 'function') {
            window.openEditModalWithError(chatId, message, errors);
        }
    });
</script>
@endif
@endsection