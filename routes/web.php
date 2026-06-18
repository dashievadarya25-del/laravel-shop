<?php

use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    //registration
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
    Route::post('/register', [RegisterController::class, 'register'])->name('register');

    //login
    Route::get('/login', [RegisterController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [RegisterController::class, 'login'])->name('login');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [RegisterController::class, 'logout'])->name('logout');

    Route::get('/profile', [RegisterController::class, 'showProfile'])->name('profile.form');
    Route::patch('/profile/{id}', [RegisterController::class, 'updateProfile'])->name('profile.update');

    Route::get('/change-password', [RegisterController::class, 'showChangePasswordForm'])->name('password.form');
    Route::post('/change-password', [RegisterController::class, 'updatePassword'])->name('password.update');
});

Route::get('/profile', function () {
    return 'Welcome to your profile!';
})->middleware('auth');



Route::get('/', function () {
    return view('welcome');
});
