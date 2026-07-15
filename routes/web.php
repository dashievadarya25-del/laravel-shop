<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    //registration
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
    Route::post('/register', [RegisterController::class, 'register'])->name('register');

    //login
    Route::get('/login', [RegisterController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [RegisterController::class, 'login'])->name('login');
});

//product
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
//category
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');


Route::middleware('auth')->group(function () {
    Route::post('/logout', [RegisterController::class, 'logout'])->name('logout');

    Route::get('/profile', [RegisterController::class, 'showProfile'])->name('profile.form');
    Route::patch('/profile/{id}', [RegisterController::class, 'updateProfile'])->name('profile.update');

    Route::get('/change-password', [RegisterController::class, 'showChangePasswordForm'])->name('password.form');
    Route::post('/change-password', [RegisterController::class, 'updatePassword'])->name('password.update');
});

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/items/{product}', [CartController::class, 'store'])->name('items.store');
    Route::patch('/items/{product}', [CartController::class, 'update'])->name('items.update');
    Route::delete('/items/{product}', [CartController::class, 'destroy'])->name('items.destroy');
    Route::delete('/', [CartController::class, 'clear'])->name('clear');
});

//Route::get('/profile', function () {
//    return 'Welcome to your profile!';
//})->middleware('auth');



Route::get('/', function () {
    return view('welcome');
});
