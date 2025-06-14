@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address_edit.css') }}">
@endsection

@section('content')
<div class="address-edit__content">
    <div class="address-edit-form__heading">
        <h1 class="address-edit-form__heading-title">住所の変更</h1>
    </div>
    <form class="address-edit-form" action="{{ route('purchase.address.update',  $exhibition->id) }}" method="post">
        @csrf
        <div class="form__group">
        <label for="post_code" class="form__label">郵便番号</label>
            <div class="form__group-content">
                <input type="text"
                    class="form__input"
                    id="post_code"
                    name="post_code"
                    value="{{ old('post_code', $shippingAddress['post_code']) }}"/>
                <p class="form__error">
                    @error('post_code')
                    {{ $message }}
                    @enderror
                </p>
            </div>
        </div>

        <div class="form__group">
            <label for="address" class="form__label">住所</label>
            <div class="form__group-content">
                <input type="text"
                    class="form__input"
                    id="address"
                    name="address"
                    value="{{ old('address', $shippingAddress['address']) }}"/>
                <p class="form__error">
                    @error('address')
                    {{ $message }}
                    @enderror
                </p>
            </div>
        </div>
        <div class="form__group">
        <label for="building" class="form__label">建物名</label>
            <div class="form__group-content">
                <input type="text"
                class="form__input"
                id="building"
                name="building"
                value="{{ old('building', $shippingAddress['building']) }}"/>
                <p class="form__error">
                    @error('building')
                    {{ $message }}
                    @enderror
                </p>
            </div>
        </div>

        <div class="form__button">
            <button class="form__button-submit" type="submit">更新する</button>
        </div>
    </form>
</div>
@endsection