<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Validator;
use Hash;
use App\Libraries\Mail;
use App\Libraries\SMS;

class CustomerPage extends Controller
{

	public function dashboard(){
		return view("customer.dashboard");
	}
	
	private function PartnerDetails($partnerId,$email = false){
		$Partner = \App\Models\Partner::find($partnerId);
		$Details = $Partner->details()->first()->toArray();
		$Location = DB::table("cities")
							->join("states","states.id","=","cities.state")
							->join("countries","countries.id","=","states.country")
							->select("cities.name as city","states.name as state","countries.name as country")
							->where("cities.id",$Details["city"])
							->get();
		$Extra = ["name"	=>	$Partner->name];
		if($email) $Extra["email"]	= $Partner->logins[0]->email;
		return array_replace($Details,(array) $Location[0],$Extra);
	}

	
	public function password(){
		return view("customer.changepassword");
	}
	
	public function changepassword(Request $Request){
		if(Hash::check($Request->old_password,$Request->user()->password)){
			$Valide = Validator::make($Request->all(),["password"	=>	"required|confirmed"],["password.required"	=> "Please provide a password.","password.confirmed"=>"New password and confirm password doesn't match."]);
			if($Valide->fails()) return redirect()->back()->withErrors($Valide)->withInput();
			$Request->user()->password = Hash::make($Request->password);
			$Request->user()->save();
			return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Password changed successfully."]);
		}
		return redirect()->back()->with(["info"=>true,"type"=>"danger","text"=>"Old password is wrong.."]);
	}
	
	public function address(){
		$user = Auth()->user();
		$Partner = \App\Models\Partner::whereCode($user->partner)->with(['Details.City.State.Country','Logins.Roles'])->whereHas('Logins.Roles',function($Q){ $Q->whereRolename('customer'); })->first();
		$Countries = $Partner->Countries->pluck('id');
		$Industries = DB::table("customer_industry")->select("code","name")->get();
		$States = ($Countries)?(\App\Models\State::select("id","name")->whereIn('country',$Countries)->get()):[];
		$Cities = ($Partner->Details->state) ? (\App\Models\City::select("id","name")->whereState($Partner->Details->state)->get()) : [];
		return view("customer.address",compact("Partner","Industries","States","Cities"));
	}
	
	public function changeaddress(Request $Request){
		$user = Auth()->user();
		$Partner = \App\Models\Partner::whereCode($user->partner)->with(['Details','Logins.Roles'])->whereHas('Logins.Roles',function($Q){ $Q->whereRolename('customer'); })->first();
		if($Partner->name != $Request->name) $Partner->update(['name'	=>	$Request->name]);
		$Partner->Details->fill($Request->only('address1','address2','city','state','industry','phone','phonecode','currency','website'));
		if(!$Partner->push()) return redirect()->back()->with(["info"=>true,"type"=>"error","text"=>"Error in updating details."]);
		if($user->email != $Request->email){
			$ES = $Partner->Logins->first()->update(['email'	=>	$Request->email]);
			if(!$ES) return redirect()->back()->with(["info"=>true,"type"=>"error","text"=>"Error in updating email."]);
		}
		return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Details Updated successfully."]);
	}
	
	public function register($seqno){
		$Data = \App\Models\CustomerRegistration::with("Customer.Details.City.State.Country","Customer.Logins","Product","Edition")->where(["customer"=>Request()->user()->partner,"seqno"=>$seqno,"registered_on"=>NULL])->first();
		return view("customer.cdd_register",compact("Data"));
	}

	public function doregister($seqno, Request $Request){
		$Data = (array) simplexml_load_string(\Illuminate\Support\Facades\Storage::get($FilePath = $Request->file("licence")->store("upload/licence")));
		$CR = \App\Models\CustomerRegistration::whereCustomer(Request()->user()->partner)->whereSeqno($seqno);
		$UA = ["lic_file"	=>	$FilePath, "version"	=>	$Data['SoftwareVersion'], "database"	=>	$Data['DatabaseName'], "requisition"	=>	$this->GetNewRequisition()];
		$CR->update($UA); $Company = \App\Models\Company::first(); $Customer = \App\Models\Customer::find(Request()->user()->partner); $Distributor = $Customer->get_distributor();
		Mail::init()->queue(new \App\Mail\NewRegRequest($CR, array_merge($Data,$UA), $Request->user(), $Company))->to($Company)->cc($Distributor)->send();
		SMS::init(new \App\Sms\RegistrationRequesToAuthor($Customer))->gateway('SMPPSMS')->send(\App\Models\Partner::find('thahir'));
		(new \App\Http\Controllers\TransactionController())->CustomerRegistrationInitialized($CR->with("customer","product","edition")->first());
		return redirect()->route("customer.dashboard")->with(["info"=>true,"type"=>"success","text"=>"Registration Request Submitted Successfully."]);
	}
	
	private function GetNewRequisition(){
		$CodePrefixChar = "MITSFTREG"; $TotalCodeLength = 15;
		$LastNum = 0; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
		$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
		$LastCode = \App\Models\CustomerRegistration::where("requisition","REGEXP",$WhereValue)->orderBy("requisition","desc")->limit(1)->pluck("requisition");
		if(!empty($LastCode[0]))
			$LastNum = intval(mb_substr($LastCode[0],$PrefixLength));
		return $CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT));
	}

	public function reginfo($seqno){
		return \App\Models\CustomerRegistration::select("registered_on","serialno","key","presale_enddate","presale_extended_to")->whereSeqno($seqno)->whereCustomer(Auth()->guard("api")->user()->partner)->first();	
	}
	
	public function myProducts($CID){
		return \App\Models\CustomerRegistration::whereCustomer($CID)->with(['Product'=>function($Q){ $Q->select('code','name'); },'Edition'=>function($Q){ $Q->select('code','name'); }])->select('created_at','registered_on','seqno','requisition','product','edition','customer','version')->get()->groupBy('product');
	}
	
	public function packages(Request $request){
		$PAry = $request->P;
		$PV = \App\Models\PackageVersion::select('product','edition','package','version_numeric','change_log')->whereStatus('APPROVED')->latest('version_sequence')->take(1)
			->with(['Product'	=>	function($Q){
				$Q->select('code','name');
			}, 'Edition'	=>	function($Q){
				$Q->select('code','name');
			}, 'Package'	=>	function($Q){
				$Q->select('code','name');
			}])->whereHas('Package',function($Q){ $Q->whereType('Update'); });
		$Packages = [];
		foreach($PAry as $PID => $EAry){
			$myPV = $PV;
			$PPV = $myPV->whereProduct($PID);
			foreach($EAry as $EID){
				$PEPV = $PPV->whereEdition($EID);
				$Data = $PEPV->first();
				if(!$Data) continue;
				if(!array_key_exists($PID,$Packages)) $Packages[$PID] = [$Data->Product->name,[]];
				$key = \App\Http\Controllers\KeyCodeController::Encode(['customer','product','edition','package','version','expiry','customer_download'],[Auth()->guard("api")->user()->partner,$PID,$EID,$Data->Package->code,$Data->version_numeric,strtotime("+2 days"),'yes']);
				if(!array_key_exists($EID,$Packages[$PID][1])) $Packages[$PID][1][$EID] = [$Data->Edition->name,$Data->version_numeric,$Data->change_log,$Data->Package->code,$Data->Package->name,Route('software.download',['key'=>$key])];
			}
		}
		return $Packages;
	}

}