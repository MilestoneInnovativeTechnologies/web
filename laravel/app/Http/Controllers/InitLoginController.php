<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\KeyCodeController;


class InitLoginController extends Controller
{

	
	public function login($code){
		if(Auth::check()){
			Auth::logout();
			return view('initlogin.reload');
		}
		event(new \App\Events\LogInitLogin($code));
		return $this->docheck($code);
	}
	
	
	public function change($code, Request $request){
		$this->validate($request,[
			'password'	=>	'required|min:6|confirmed'
		]);
		$Ary = $this->GetArray($code); if($Ary === false) return view('initlogin.invalid');
		$Partner = $this->GetPartner($Ary['id']);
		$Valide = $this->isValide($Partner,$Ary);
		if(!$Valide) return view('initlogin.invalid');
		$this->changePassword($Partner, $request->password);
		Auth::loginUsingId($Partner->id);
		return redirect()->route((new \App\Http\Controllers\Auth\LoginController())->RDT());
	}
	
	private function docheck($code){
		$Ary = $this->GetArray($code); if($Ary === false) return view('initlogin.invalid');
		if($this->isExpired($Ary)) return view('initlogin.expired');
		$Valide = $this->isValide($this->GetPartner($Ary['id']),$Ary);
		if($Valide) return view('initlogin.password');
		return view('initlogin.invalid');
	}
	
	private function GetArray($code){
		list($Fields,$Values) = KeyCodeController::Decode($code);
		if(!is_array($Fields) || !is_array($Values) || count($Fields) != count($Values)) return false;
		return array_combine($Fields,$Values);
	}
	
	private function GetPartner($ID){
		return \App\Models\PartnerLogin::find($ID);
	}
	
	private function isExpired($Ary){
		return array_key_exists('expiry', $Ary)?($Ary['expiry'] < time()):true;
	}
	
	private function isValide($DBLogin,$CodeArray){
		$DB = $DBLogin->toArray();
		foreach($CodeArray as $Field => $Value){
			if($Field == 'expiry') continue;
			if($DB[$Field] != $Value)
				return false;
		}
		return true;
	}
	
	private function changePassword($Partner, $Password){
		return $Partner->update(['password'=>bcrypt($Password)]);
	}
	
}
