<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated as Middleware;
use Illuminate\Http\Request;
use Closure;

class RedirectIfAuthenticated extends Middleware
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $route = RouteServiceProvider::HOME;

        foreach ($guards as $guard) {
            if (auth()->guard($guard)->check()) {
                return redirect($route);
            }
        }

        return $next($request);
    }
}
