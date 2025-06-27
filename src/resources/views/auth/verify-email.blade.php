@extends('layouts.auth_app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="register__content">
    <div class="verify-email__content">
        @if (session('message'))
            <div class="alert-success">
                {{ session('message') }}
            </div>
        @endif

        <p class="verify-email__message">
            登録していただいたメールアドレスに認証メールを送付しました。<br>
            メール認証を完了してください。
        </p>

        <div class="verification__button">
            <button class="verification__button-submit" onclick="location.href='{{ route('verification.notice') }}'">
                認証はこちらから
            </button>
        </div>

        <div class="resend-email">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="resend-email__link">
                    認証メールを再送する
                </button>
            </form>
        </div>
    </div>
</div>
@endsection