<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Laravel Shop') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-light bg-light mb-4">
    <div class="container">
        <a href="/" class="navbar-brand">Laravel Shop</a>
    </div>
</nav>

<main class="container">
    @yield('content')  {{-- сюда вставляется контент каждой страницы --}}
</main>
</body>
</html>

