<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Request;

use DB;
use App\User;
use Jenssegers\Agent\Agent;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';
	
	 public function showLoginForm()
    {       
		$token = Request::query('token', false);
		
        $mobile_id = Request::query('sid', false);
		
		//dd($token);

        if ($token){
            setcookie("messenger_token", $token);
            setcookie("mobile_id", $mobile_id);
            return view('auth.login');
        }else{
            return view('auth.login');
        }
    }
	
	protected function authenticated(\Illuminate\Http\Request  $request, $user)
    {
        //
        $cm_userid = $user->id;
        //dd($cm_userid);
		$this->checkPushNotToken($cm_userid);
        //$this->saveToLogs($cm_userid);
		
    }
	
	/* SAVE PUSH NOTIFICATION TOKEN IN DB IF NOT EXISTS */  
    public function checkPushNotToken($cm_userid){
       
		if( isset($_COOKIE['messenger_token'])){
			$token = (string) $_COOKIE['messenger_token'];
			$mobile_id = (string) $_COOKIE['mobile_id'];

		}else{
			return view('auth.login');
			exit();
		}

        if ($token !== null) {

            $detect = new Agent();
            //Check for a specific platform with the help of the magic methods:
            if( $detect->isiOS() ){
                $type = "ios";
            }else if( $detect->isAndroidOS() ){
                $type = "android";
            }else{
                $type = "none";
            }
			DB::table('users')
			->where('id', $cm_userid)
			->update([
				'token' => $token,
				'sim_id' => $mobile_id,
				'type' => $type ]);
				
        }
    } 
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
