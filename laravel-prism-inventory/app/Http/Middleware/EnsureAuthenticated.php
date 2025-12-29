<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('user')) {
            return redirect()->route('login.form');
        }
        return $next($request);
    }
}