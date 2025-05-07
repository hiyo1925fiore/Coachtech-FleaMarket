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
        <p>プロフィール</p>
    </form>
</div>
@endsection