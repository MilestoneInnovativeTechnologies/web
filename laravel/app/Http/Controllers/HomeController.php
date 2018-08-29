<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Mail\ProductDownloadLinks;

use App\Libraries\Mail;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home.home');
    }
	
	
	public function sdl(Request $request){
		$Product = $request->product; $To = $request->email;
		$ORM = Product::withoutGlobalScopes()->whereCode($Product)->with([
			'Editions'	=>	function($Q) use ($Product) {
			$Q->wherePivot('product',$Product)->wherePrivate('NO')->oldest('products_editions.level');
		},
			'Editions.Packages'	=>	function($Q) use ($Product) {
			$Q->wherePivot('product',$Product)->whereType('Onetime');
		}
		])->whereActive('1')->wherePrivate('NO')->first();
		//return $ORM;
		Mail::init()->queue(new ProductDownloadLinks($ORM,$To))->to($To)->Send();
	}
	
	public function features($pid){
		$ORM = \App\Models\Product::withoutGlobalScopes()->whereCode($pid)->with([
			'Features',
			'Editions'	=>	function($Q){
				$Q->wherePrivate('NO')->oldest('products_editions.level');
			},
			'Editions.Features'	=>	function($Q) use($pid){
				$Q->wherePivot('product',$pid);
			}
		])->first();
		
		$ORM->Features = $ORM->Features->map(function($item, $key){
			return [$item->id, $item->name, $item->pivot->value];
		});
		
		$ORM->Editions = $ORM->Editions->mapWithKeys(function($item){
			return [$item->name => $item->Features->map(function($item, $key){
				return [$item->id, $item->name, $item->pivot->value];
			})];
		});
		return ['features'	=>	$ORM->Features, 'editions'	=>	$ORM->Editions];
	}
	
	
	public function forget(){
		return view('home.forget');
	}
	
	public function send_reset_link(Request $request){
		$email = $request->email;
		$ORM = \App\Models\PartnerLogin::whereEmail($email);
		if($ORM->get()->isEmpty()) return redirect()->route('password.forget')->with(["info"=>true,"type"=>"danger","text"=>"The email entered is not in our records, Please try again."])->withInput();
		$Logins = $ORM->first(['id','partner','email']);
		$pArr = ['id','partner','email','expiry'];
		$vArr = [$Logins->id,$Logins->partner,$Logins->email,strtotime("+18 Hours")];
		$Code = \App\Http\Controllers\KeyCodeController::Encode($pArr,$vArr);
		Mail::init()->queue(new \App\Mail\GuestLoginReset($Logins,$Code))->to($Logins->partner)->send();
		event(new \App\Events\LogPasswordResetRequest($Logins->partner, $Logins->email, $Code));
		return view('home.forget')->with(["info"=>true,"type"=>"success","text"=>"Rest link successfully mailed to ".$email."<br>Follow instructions in the mail to reset password."]);
	}
	
	
	
}
