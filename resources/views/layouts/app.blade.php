<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel Shop') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-light bg-light mb-4">
    <div class="container">
        <a href="{{ route('products.index') }}" class="navbar-brand">Laravel Shop</a>
        @php($cartCount = collect(session('cart.items', []))->sum())
        <a href="{{ route('cart.index') }}" class="btn btn-outline-primary btn-sm">
            Корзина
            <span class="badge text-bg-secondary ms-1" data-cart-count>{{ $cartCount }}</span>
        </a>

        <div class="d-flex align-items-center gap-2">

            @guest
                <a href="{{ route('login.form') }}" class="btn btn-primary btn-sm">Вход</a>
                <a href="{{ route('register.form') }}" class="btn btn-outline-secondary btn-sm">Регистрация</a>
            @endguest

            @auth
                <a href="{{ route('profile.form') }}" class="btn btn-outline-secondary btn-sm">Профиль</a>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">Выход</button>
                </form>
            @endauth
        </div>
    </div>
</nav>

<main class="container">
    @yield('content')  {{-- сюда вставляется контент каждой страницы --}}
</main>
</body>
</html>

