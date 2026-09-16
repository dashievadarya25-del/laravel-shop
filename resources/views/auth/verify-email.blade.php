{{-- resources/views/auth/verify-email.blade.php --}}
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Подтверждение Email</title>
</head>
<body>
<div style="max-width: 500px; margin: 50px auto; text-align: center;">
    <h2>Подтвердите ваш электронный адрес</h2>

    @if (session('status') == 'verification-link-sent')
    <p style="color: green;">
        Новая ссылка для подтверждения была отправлена на ваш email.
    </p>
    @endif

    <p>Прежде чем продолжить, пожалуйста, перейдите по ссылке в письме, которое мы вам отправили.</p>

    <!-- Кнопка повторной отправки -->
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit">Отправить письмо повторно</button>
    </form>

    <!-- Ссылка для выхода, если пользователь хочет зайти под другим аккаунтом -->
    <form method="POST" action="{{ route('logout') }}" style="margin-top: 20px;">
        @csrf
        <button type="submit" style="background: none; border: none; color: blue; text-decoration: underline; cursor: pointer;">
            Выйти
        </button>
    </form>
</div>
</body>
</html>
