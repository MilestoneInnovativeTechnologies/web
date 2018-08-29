<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Cookie;
use Illuminate\Support\Facades\Auth;
use App\Models\PartnerLogin;

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
    protected $redirectTo = 'dashboard';

		protected function redirectTo(){
			$user = Request()->user();
			if($user->roles()->count() > 1){
				return "roleselect";
			}	else {
				$roleObj = $user->roles->first();
				session()->put("_role",$roleObj->code);
				session()->put("_rolename",$roleObj->name);

				$_company = ($user->roles->first()->name == "company")?1:0;
				session()->put("_company",$_company);
				$api_token = implode("|",[$roleObj->code,str_random(15),$_company,$roleObj->name]);
				
				$user->update(["api_token"	=> $api_token]);
				Cookie::queue("api_token",$api_token);
				
				event(new \App\Events\LogUserLogin($user,$roleObj->name));
			}
			return $this->redirectTo;
		}

		public function logout()
    {
				$this->guard()->logout();

        session()->flush();

        session()->regenerate();
			
				Cookie::queue(Cookie::forget('api_token'));

        return redirect('home');
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
	
	public function RDT(){
		return $this->redirectTo();
	}
	
}
