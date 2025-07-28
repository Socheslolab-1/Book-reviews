<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter; // Додано
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request; // Додано

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
            // Реєстрація обмеження швидкості для маршруту 'reviews'
            RateLimiter::for('reviews', function (Request $request) {
            // Встановлюємо обмеження на 5 запитів за годину
            // Використовуємо ідентифікатор користувача (якщо він є) або IP-адресу запиту для ідентифікації
            return Limit::perHour(5)->by($request->user()?->id ?: $request->ip());
        });

    }
}
