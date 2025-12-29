<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(Router $router): void
    {
        // Ensure aliases exist even if Kernel cache was stale
        $router->aliasMiddleware('auth.session', \App\Http\Middleware\EnsureAuthenticated::class);
        $router->aliasMiddleware('role.admin', \App\Http\Middleware\EnsureAdmin::class);
    }
}