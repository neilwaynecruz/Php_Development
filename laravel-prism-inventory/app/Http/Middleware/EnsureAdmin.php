<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if ((string) session('role') !== 'admin') {
            session()->flash('message', '<div class="alert alert-danger">Access denied: Admins only.</div>');
            return redirect()->route('products.index');
        }
        return $next($request);
    }
}