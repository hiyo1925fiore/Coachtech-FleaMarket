<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @yield('head')
    <title>coachtechフリマ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    @livewireStyles
</head>
<body>
    <header class="header">
        <div class="header__inner">
            @auth
            <!-- ログイン後 -->
            <a class="header__link--item-list" href="/?page=mylist">
                <img src="{{asset('image/logo.svg')}}" alt="COACHTECH" class="header__logo">
            </a>
            @else
            <!-- ログイン前 -->
            <a class="header__link--item-list" href="/">
                <img src="{{asset('image/logo.svg')}}" alt="COACHTECH" class="header__logo">
            </a>
            @endauth

            @if( !in_array(Route::currentRouteName(), ['register', 'login', 'verification.notice']) )
            <!-- 検索コンポーネント -->
            @livewire('item-search-component')

            <nav class="header__nav">
                <ul class="header__list">
                    <li class="header__list-item">
                        @auth
                        <!-- ログイン後 -->
                        <form action="/logout" class="header__form" method="post">
                            @csrf
                            <button class="header__form--logout" type="submit">ログアウト</button>
                        </form>
                        @else
                        <!-- ログイン前 -->
                        <a class="header__link--login" href="/login">ログイン</a>
                        @endauth
                    </li>

                    <li class="header__list-item">
                        <a class="header__link--mypage" href="/mypage">マイページ</a>
                    </li>

                    <li class="header__list-item">
                        <a class="header__link--sell" href="/sell">出品</a>
                    </li>
                </ul>
            </nav>
            @endif
        </div>
    </header>
    <main>
        @yield('content')
    </main>
    @livewireScripts
</body>
</html>