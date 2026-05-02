<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $route = auth()->user()->role === 'admin'
                ? route('admin.dashboard')
                : route('user-dashboard');

            return redirect($route);
        }

        return $next($request);
    }
}
