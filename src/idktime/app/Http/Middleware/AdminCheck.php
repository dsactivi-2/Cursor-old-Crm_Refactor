<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class AdminCheck
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
        if (auth()->user()->user_role_id == 1)
            return $next($request);

        Session::flash('error', 'Nemate permisije za odabranu akciju.');
        return back();
    }
}
