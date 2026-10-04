<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Регистрация вашего Middleware для ролей
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        // Исключение вебхука ЮKassa из проверки CSRF (объединено сюда)
        $middleware->validateCsrfTokens(except: [
            'payments/yookassa/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })
    ->create(); // <-- Теперь create() вызывается строго в самом конце!
