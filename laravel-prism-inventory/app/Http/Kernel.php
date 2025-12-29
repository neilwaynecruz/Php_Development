<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // ...

    protected $middlewareAliases = [
        // core aliases...
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'guest' => \Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,

        // your custom aliases
        'auth.session' => \App\Http\Middleware\EnsureAuthenticated::class,
        'role.admin'   => \App\Http\Middleware\EnsureAdmin::class,
    ];
}