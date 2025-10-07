@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/exhibition.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="exhibition-form__heading">
        <h1 class="exhibition-form__heading-title">商品の出品</h1>
    </div>

    <form class="exhibition-form" action="/sell" method="post"  enctype="multipart/form-data">
        @csrf
        <div class="form__group--image">
            <label class="exhibition-form__image-label" for="img_url">商品画像</label>

            <div class="exhibition-form__image-upload-container">
                <div class="exhibition-form_image-preview-container" id="image-preview-container">
                </div>

                <div class="exhibition-form__file-input-wrapper">
                    <input class="exhibition-form__file" type="file" name="img_url" id="img_url" style="display: none;" accept="image/*">
                    <label for="img_url" class="exhibition-form__file-select-button">画像を選択する</label>
                </div>
                <p class="form__error">
                    @error('img_url')
                    {{ $message }}
                    @enderror
                </p>
            </div>
        </div>

        <h2 class="exhibition-form__section-title">商品の詳細</h2>
        <div class="form__group--categories">
            <label class="exhibition-form__categories-title">
                カテゴリー
            </label>
            <div class="exhibition-form__category-inputs">
                @foreach($categories as $category)
                <div class="exhibition-form__category-option">
                    <input
                        class="exhibition-form__category-input"
                        type="checkbox"
                        name="category_id[]"
                        id="{{ strtolower(str_replace(' ', '-', $category->category)) }}"
                        value="{{ $category->id }}"
                        {{ (in_array($category->id, old('category_id', []))) ? 'checked' : '' }}
                    >
                    <label
                        class="exhibition-form__category-label"
                        for="{{ strtolower(str_replace(' ', '-', $category->category)) }}"
                    >
                        <span class="exhibition-form__category-text">{{ $category->category }}</span>
                    </label>
                </div>
                @endforeach
            </div>
            <p class="form__error">
                @error('category_id')
                {{ $message }}
                @enderror
            </p>
        </div>

        <div class="form__group">
            <label class="exhibition-form__label">
                商品の状態
            </label>
            <div class="exhibition-form__select-inner">
                <label class="exhibition-form__select-label">
                    <select class="exhibition-form__select" name="condition_id" id="">
                        <option disabled selected>選択してください</option>
                        @foreach($conditions as $condition)
                        <option value="{{ $condition->id }}" {{ old('condition_id')==$condition->id ? 'selected' : '' }}>{{ $condition->condition }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <p class="form__error">
                @error('condition_id')
                {{ $message }}
                @enderror
            </p>
        </div>

        <h3 class="exhibition-form__section-title">商品名と説明</h3>
        <div class="form__group">
            <label class="exhibition-form__label" for="name">
                    商品名
            </label>
            <input class="exhibition-form__input" type="text" name="name" id="name" value="{{ old('name') }}">
            <p class="form__error">
                @error('name')
                {{ $message }}
                @enderror
            </p>
        </div>

        <div class="form__group">
            <label class="exhibition-form__label" for="brand">
                    ブランド名
            </label>
            <input class="exhibition-form__input" type="text" name="brand" id="brand" value="{{ old('brand') }}">
            <p class="form__error">
                @error('brand')
                {{ $message }}
                @enderror
            </p>
        </div>

        <div class="form__group">
            <label class="exhibition-form__label" for="description">
                商品の説明
            </label>
            <textarea class="exhibition-form__textarea" name="description" id="description">{{ old('description') }}</textarea>
            <p class="form__error">
                @error('description')
                {{ $message }}
                @enderror
            </p>
        </div>

        <div class="form__group">
            <label class="exhibition-form__label" for="price">
                    販売価格
            </label>
            <div class="price-input-container">
                <input class="exhibition-form__price-input" type="text" name="price" id="price" value="{{ old('price') }}">
            </div>
            <p class="form__error">
                @error('price')
                {{ $message }}
                @enderror
            </p>
        </div>

        <div class="form__button">
            <button class="form__button-submit" type="submit">出品する</button>
        </div>
    </form>
</div>
<!-- 選択した画像のプレビュー＆画像選択時のみ画像名を表示する -->
<script src="{{ asset('js/preview_image.js') }}"></script>
@endsection