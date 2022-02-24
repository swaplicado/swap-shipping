<?php

namespace App\Http\Middleware;

use Closure;
use App\MiddlewareUtils\Home;

class HomeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $home = Home::home(\Auth::user());
        session(['home' => $home]);

        return $next($request);
    }
}
