<?php

namespace App\Http\Middleware;

use Closure;

class canCreateGroups
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
        if($request->user()->canCreateGroups()) {
            return $next($request);
        }
        return redirect('home');
    }
}
