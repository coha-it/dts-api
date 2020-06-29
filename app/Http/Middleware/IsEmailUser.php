<?php

namespace App\Http\Middleware;

use Closure;

class IsEmailUser
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
        if($request->user()->isEmailUser()) {
            return $next($request);
        }
        return redirect('home');
    }
}
