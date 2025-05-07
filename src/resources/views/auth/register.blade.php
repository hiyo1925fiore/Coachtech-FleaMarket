@extends('layouts.auth_app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="register__content">
    <div class="register-form__heading">
        <h1 class="register-form__heading-title">会員登録</h1>
    </div>

    <form class="register-form" action="/register" method="post" novalidate>
        @csrf
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">ユーザー名</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" class="form__input" name="name" value="{{ old('name') }}"/>
                </div>
                <p class="form__error">
                    @error('name')
                    {{ $message }}
                    @enderror
                </p>
            </div>
        </div>
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">メールアドレス</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="email" class="form__input" name="email" value="{{ old('email') }}"/>
                </div>
                <p class="form__error">
                    @error('email')
                    {{ $message }}
                    @enderror
                </p>
            </div>
        </div>
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">パスワード</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="password" class="form__input" name="password"/>
                </div>
                <p class="form__error">
                    @error('password')
                    {{ $message }}
                    @enderror
                </p>
            </div>
        </div>
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">確認用パスワード</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="password" class="form__input" name="password_confirmation"/>
                </div>
                <p class="form__error">
                    @error('password_confirmation')
                    {{ $message }}
                    @enderror
                </p>
            </div>
        </div>
        <div class="form__button">
            <button class="form__button-submit" type="submit">登録する</button>
        </div>
    </form>
    <div class="login__link">
        <a class="login__button-submit" href="/login">ログインはこちら</a>
    </div>
</div>
@endsection