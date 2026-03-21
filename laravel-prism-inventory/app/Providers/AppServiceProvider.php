<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

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
        // Use Bootstrap pagination views (if you use pagination anywhere)
        Paginator::useBootstrapFive();

        // Define the 'two-factor' rate limiter required by Fortify
        RateLimiter::for('two-factor', function (Request $request) {
            // Limit based on session login.id or the IP address
            $key = optional($request->user())->getAuthIdentifier()
                ?? $request->session()->get('login.id')
                ?? $request->ip();

            return Limit::perMinute(5)->by($key);
        });
    }
}