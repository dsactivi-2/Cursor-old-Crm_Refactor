<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // dd($request->get('sid')."vvdsf");
        if (Auth::guard($guard)->check()) {
            if(!empty($request->get('sid'))){
                $check = new HomeController;
				if(!$check->checkSimIdCard($request->get('sid'))){
                    return redirect('/phonenumber');
				}
				
			}
			return redirect('/home');
        }
        
        return $next($request);
		//dd($request->get('token'));
		
    }
}
