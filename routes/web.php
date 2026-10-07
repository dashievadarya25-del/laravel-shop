<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\OrderPaymentAdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductManagementController;
use App\Http\Controllers\YookassaController;
use App\Models\Order;
use App\Notifications\WelcomeNotification;
use App\Services\OrderService;
use App\Services\YooKassaPaymentService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // registration
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
    Route::post('/register', [RegisterController::class, 'register'])->name('register');

    // login
    Route::get('/login', [RegisterController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [RegisterController::class, 'login'])->name('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [RegisterController::class, 'showVerifyEmailNotice'])
        ->middleware('auth')
        ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $user = $request->user();

        if (! $user->hasVerifiedEmail()) {
            $request->fulfill();

            $user->notify(new WelcomeNotification());
        }

        return redirect()->route('products.index')
            ->with('status', 'Email успешно подтвержден! Добро пожаловать в каталог.');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Ссылка для подтверждения отправлена повторно!');
    })->middleware('throttle:6,1')->name('verification.send');
});

// product
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// category
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

// Авторизованная зона пользователя
Route::middleware('auth')->group(function () {
    Route::post('/logout', [RegisterController::class, 'logout'])->name('logout');

    Route::get('/profile', [RegisterController::class, 'showProfile'])->name('profile.form');
    Route::patch('/profile/{id}', [RegisterController::class, 'updateProfile'])->name('profile.update');

    Route::get('/change-password', [RegisterController::class, 'showChangePasswordForm'])->name('password.form');
    Route::post('/change-password', [RegisterController::class, 'updatePassword'])->name('password.update');
    Route::patch('/profile/addresses/{address}/default', [RegisterController::class, 'makeAddressDefault'])->name('profile.addresses.default');
    Route::post('/profile/addresses', [RegisterController::class, 'storeAddress'])->name('profile.addresses.store');

    // Заказы
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])
        ->name('orders.status.update');

    // ИНТЕГРАЦИЯ YOOKASSA

    Route::get('/orders/{order}/pay', [YookassaController::class, 'pay'])
        ->name('yookassa.pay');

    Route::get('/payments/yookassa/return/{order}', [YookassaController::class, 'returnBack'])
        ->name('payments.yookassa.return');

    Route::get('/api/payments/yookassa/status/{order}', function (Order $order): JsonResponse {
        if ($order->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $lastPayment = $order->payments()
            ->where('provider', 'yookassa')
            ->orderByDesc('created_at')
            ->first();

        return response()->json([
            'order_status' => $order->status,
            'payment_status' => $lastPayment?->status ?? 'none'
        ]);
    })->name('api.payments.yookassa.status');
});

// Админ-панель
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('users', UserController::class);
        Route::patch('users/{user}/password', [UserController::class, 'resetPassword'])
            ->name('users.password');

        Route::resource('orders', OrderAdminController::class);

        Route::resource('products', ProductManagementController::class);

        Route::resource('payments', OrderPaymentAdminController::class)
            ->only(['index', 'show']);
    });

// Корзина
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/items/{product}', [CartController::class, 'store'])->name('items.store');
    Route::patch('/items/{product}', [CartController::class, 'update'])->name('items.update');
    Route::delete('/items/{product}', [CartController::class, 'destroy'])->name('items.destroy');
    Route::delete('/', [CartController::class, 'clear'])->name('clear');
});


// ИНТЕГРАЦИЯ YOOKASSA (ВНЕШНИЙ WEBHOOK)

Route::post('/payments/yookassa/webhook', [YookassaController::class, 'webhook']);

Route::get('/', function () {
    return view('main');
});

// Временный адрес для ручной проверки: http://localhost:82/test-check/{id_заказа}
Route::get('/test-check/{order}', function (Order $order, YooKassaPaymentService $paymentService, OrderService $orderService) {
    // Находим платеж ЮKassa для этого заказа
    $lastPayment = $order->payments()->where('provider', 'yookassa')->first();

    if (!$lastPayment) {
        return 'Платеж по ЮKassa для этого заказа не найден в БД.';
    }

    // Принудительно запрашиваем реальный статус напрямую у API ЮKassa (GET /payments/{id})
    $yookassaData = $paymentService->fetchPayment($lastPayment->external_payment_id);

    if ($yookassaData) {
        // Запускаем синхронизацию. Сервис увидит статус succeeded и сам переведет заказ в "Оплачен"
        $paymentService->synchronizePayment($lastPayment, $yookassaData, 'payment.succeeded', $orderService);
        return 'Статус успешно проверен по API ЮKassa! Перейдите в "Мои заказы", надпись поменялась.';
    }

    return 'Не удалось получить ответ от серверов ЮKassa.';
});
