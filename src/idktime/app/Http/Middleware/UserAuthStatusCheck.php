<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use Auth;

class UserAuthStatusCheck
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
        if (auth()->user()) {
            if (auth()->user()->user_status_id == 2) {

                Auth::logout();
                $request->session()->flush();
                Session::flash('error', 'Vaš račun je suspendovan.');
                
                return redirect()->route('login');
            }

            return $next($request);
        } else {
            Session::flash('error', 'Prijavite se za pristup aplikaciji.');
            return back();
        }
    }
}
