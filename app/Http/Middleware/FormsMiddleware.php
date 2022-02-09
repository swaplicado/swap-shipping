<?php

namespace App\Http\Middleware;

use Closure;
use App\Forms\Forms;

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

        return $next($request);
    }
}
