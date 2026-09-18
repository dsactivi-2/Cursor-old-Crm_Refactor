<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class PermissionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next , $permission)
    {   
        if (auth()->user()->user_role_id == 1)
            return $next($request);
        else
            if (auth()->user()->permissions()->where('slug' , $permission)->first())
                return $next($request);
            
        Session::flash('error' , 'Nemate permisije za odabranu akciju.');
        return back();
    }
}
