@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile_edit.css') }}">
@endsection

@section('content')
<div class="profile-edit__content">
    <div class="profile-form__heading">
        <h1 class="profile-form__heading-title">プロフィール設定</h1>
    </div>

    <form class="profile-form" action="/mypage/profile" method="post"  enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="profile-image-section">
            <div class="profile-form__image-upload-container">
                <div class="profile-form__image-preview-container" id="image-preview-container">
                    @if(isset($profile->img_url) && $profile->img_url)
                    <img src="{{ asset('storage/' . $profile->img_url) }}" alt="プロフィール画像" class="profile-preview-image" id="preview-image">
                    @else
                    <div class="profile-placeholder-circle" id="placeholder-circle"></div>
                    @endif
                </div>

                <div class="profile-form__file-input-wrapper">
                    <input class="profile-form__file" type="file" name="img_url" id="img_url" style="display: none;" accept="image/*">
                    <label for="img_url" class="profile-form__file-select-button">画像を選択する</label>
                </div>
            </div>
        </div>

        <div class="profile-form__group">
        <label for="name" class="profile-form-label">ユーザー名</label>
            <div class="profile-form__group-content">
                <div class="profile-form__input--text">
                    <input type="text" class="profile-form__input" name="name" id="name" value="{{ old('name', $user->name ?? '') }}"/>
                </div>
                <p class="profile-form__error">
                    @error('name')
                    {{ $message }}
                    @enderror
                </p>
            </div>
        </div>

        <div class="profile-form__group">
        <label for="post_code" class="profile-form-label">郵便番号</label>
            <div class="profile-form__group-content">
                <div class="profile-form__input--text">
                    <input type="text" class="profile-form__input" name="post_code" id="post_code" value="{{ old('post_code', $profile->post_code ?? '') }}"/>
                </div>
                <p class="profile-form__error">
                    @error('post_code')
                    {{ $message }}
                    @enderror
                </p>
            </div>
        </div>

        <div class="profile-form__group">
        <label for="address" class="profile-form-label">住所</label>
            <div class="profile-form__group-content">
                <div class="profile-form__input--text">
                    <input type="text" class="profile-form__input" name="address" id="address" value="{{ old('address', $profile->address ?? '') }}"/>
                </div>
                <p class="profile-form__error">
                    @error('address')
                    {{ $message }}
                    @enderror
                </p>
            </div>
        </div>

        <div class="profile-form__group">
        <label for="building" class="profile-form-label">建物名</label>
            <div class="profile-form__group-content">
                <div class="profile-form__input--text">
                    <input type="text" class="profile-form__input" name="building" id="building" value="{{ old('building', $profile->building ?? '') }}"/>
                </div>
                <p class="profile-form__error">
                    @error('building')
                    {{ $message }}
                    @enderror
                </p>
            </div>
        </div>

        <div class="profile-form__button">
            <button class="profile-form__button-submit" type="submit">更新する</button>
        </div>
    </form>
</div>
<!-- 選択した画像のプレビューを表示する -->
<script src="{{ asset('js/profile_preview_image.js') }}"></script>
@endsection