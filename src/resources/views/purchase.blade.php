@extends('layouts.app')

@section('head')
<!-- stripe遷移用リンク -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="content">
    <form class="purchase-form" method="POST" action="{{ route('purchase.store', $exhibition->id) }}">
        @csrf
        <div class="purchase-content__left">
            <div class="exhibition-info">
                <img class="exhibition-info__image" src="{{ Storage::url($exhibition->img_url) }}" alt="{{ $exhibition->name }}">
                <div class="exhibition-info__detail">
                    <h2 class="exhibition-info__name">{{ $exhibition->name }}</h2>
                    <p class="exhibition-info__price"><span class="exhibition-info__yen">&yen;&nbsp;</span>{{ number_format($exhibition->price) }}</p>
                </div>
            </div>

            <div class="payment-select">
                <h3 class="purchase__content-title">支払い方法</h3>
                <div class="payment-form__select-inner">
                    <label class="payment-form__select-label">
                        <select class="payment-form__select" name="payment" id="">
                            <option disabled {{ old('payment') ? '' : 'selected' }}>選択してください</option>
                            <option value="1" {{ old('payment') == '1' ? 'selected' : '' }}>コンビニ支払い</option>
                            <option value="2" {{ old('payment') == '2' ? 'selected' : '' }}>カード支払い</option>
                        </select>
                    </label>
                </div>
                <p class="payment-form__error-message">
                    @error('payment')
                    {{ $message }}
                    @enderror
                </p>
            </div>

            <div class="purchase-address">
                <div class="purchase-address__header">
                    <h4 class="purchase__content-title">配送先</h4>
                    <a class="address-edit__link" href="{{ route('purchase.address.edit', $exhibition->id) }}">変更する</a>
                </div>
                <p class="purchase-address__text">〒 {{ $shippingAddress['post_code'] }}</p>
                <p class="purchase-address__text">{{ $shippingAddress['address'] }}&ensp;{{ $shippingAddress['building'] ? ' ' . $shippingAddress['building'] : '' }}</p>
            </div>
        </div>

        <div class="purchase-content__right">
            <table class="purchase-info">
                <tr class="purchase-info__row">
                    <th class="purchase-info__title">商品代金</th>
                    <td class="purchase-info__price">
                        {{ number_format($exhibition->price) }}
                    </td>
                </tr>
                <tr class="purchase-info__row">
                    <th class="purchase-info__title">支払い方法</th>
                    <td class="purchase-info__payment"></td>
                </tr>
            </table>

            <button type="submit" class="purchase-button">購入する</button>
        </div>
    </form>
</div>
<script src="{{ asset('js/payment.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
@endsection