<?php

namespace App\Http\Middleware;

use Closure;
use App\MiddlewareUtils\Forms;

class FormsMiddleware
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
        $form = Forms::createForm(\Auth::user());
        session(['form' => $form]);
        
        $myProfile = Forms::myProfile(\Auth::user());
        session(['myProfile' => $myProfile]);

        $route = Forms::profileRoute(\Auth::user());
        session(['route' => $route]);

        return $next($request);
    }
}
