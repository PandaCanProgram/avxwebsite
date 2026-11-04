<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        RateLimiter::for('inquiry-email-checks', fn (Request $request): Limit => Limit::perMinute(30)->by($request->ip()));

        RateLimiter::for('inquiries', function (Request $request): array {
            $email = $request->input('email');
            $normalizedEmail = is_string($email) ? strtolower(trim($email)) : '';

            return [
                Limit::perMinutes(10, 5)->by('ip:'.$request->ip()),
                Limit::perMinutes(10, 3)->by('email:'.hash('sha256', $normalizedEmail)),
            ];
        });
    }
}
