<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Регистрируем шлюз 'admin-panel'
        Gate::define('admin-panel', function (User $user) {
            // Метод hasRole проверит, есть ли у юзера роль со slug = 'admin'
            return $user->hasRole('admin');
        });
    }
}
