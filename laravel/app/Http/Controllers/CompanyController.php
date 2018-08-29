<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Partner;
use App\Models\Product;

use DB;
use Validator;
use Hash;
use App\Libraries\Mail;
use App\Libraries\SMS;

use App\Mail\CustomerRegistrationSuccess;

class CompanyController extends Controller
{
	
	private $items = 500;
	private $page = 0;

 		
		public function __construct(){
			
			$this->middleware(function($request, $next){
				$param = "code";
				if(gettype($request->route($param)) == "object" && in_array($request->route($param)->status,["ACTIVE","PENDING"])) return $next($request);
				if(gettype($request->route($param)) == "string" && in_array(Partner::find($request->route($param))->status,["ACTIVE","PENDING"])) return $next($request);
				return redirect()->route($param.".error");
			})->only(["show"]);
			
		}

	public function index(){
		return view('company.dashboard');
		list($Items,$Pages) = $this->PageItem(NULL,NULL,true);
		$Partners = $this->AllPartners($Items,$Pages);
		$PE = Product::select("code","name","private")->with(["editions"=>function($q){ $q->select("code","name","private"); }])->whereActive("1")->has("editions")->get()->mapWithKeys(function($item){
			return [$item->code => ["name"	=>	$item->name, "private"	=>	$item->private, "editions"	=>	$item->editions->mapWithKeys(function($item2){
				return [$item2->code => ["name"	=>	$item2->name, "private"	=>	$item2->private]];
			})]];
		});
		return view("company.index",compact("Partners","PE","Items"));
	}

	private function CustomerProducts($items, $page, $parent=false, $childs=false){
		$SelectedArray = ['partners.code as code', 'partners.name as name', 'pp.product as product_code', 'products.name as product', 'pp.edition as edition_code', 'editions.name as edition', 'pp.created_at', 'pp.registered_on'];
		if($childs) array_push($SelectedArray,"parent.code as parent_code","parent.name as parent");
		$ORM = Partner::select($SelectedArray)
			->join('partner_roles',function($join){ $join->on('partner_roles.partner','=','partners.code'); $join->on('partner_roles.status','=',DB::raw('"ACTIVE"')); })
			->join('roles',function($join){ $join->on('roles.code','=','partner_roles.role'); $join->on('roles.status','=',DB::raw('"ACTIVE"')); })
			->join('customer_registrations as pp','pp.customer','partners.code')
			->join('partner_relations','partner_relations.partner','partners.code')
			->join('products','products.code','pp.product')->join('editions','editions.code','pp.edition');
		
		if($childs) $ORM->join("partners as parent","partner_relations.parent","parent.code");
		
		if($parent) $ORM->where('partner_relations.parent',DB::raw('"'.$parent.'"'));
		elseif($childs) $ORM->whereIn('partner_relations.parent',$childs);
		else $ORM->whereIn('partners.status',['ACTIVE','PENDING'])->where('roles.name','customer');
		$ORM->orderBy("pp.created_at","DESC");

		if($page === NULL) $ORM->where(DB::raw('DATEDIFF(now(),`partners`.`created_at`)'),"<=",DB::raw($items));
		else $ORM->limit($items)->offset($page*$items);
		
		return $ORM->get();
	}

	private function AllPartners($items,$page,$role=false,$parent=false){
		$ORM = Partner::select("partners.code as code","partners.name as name",DB::raw('GETCOUNTRIES(partners.code) as countries'),DB::raw('GETEMAILS(partners.code) as emails')
													 ,"partner_relations.parent as parent_code", "partners2.name as parent", "roles2.rolename as parent_role"
													 ,"roles.name as role"
													 ,"customer_industry.code as industry_code","customer_industry.name as industry"
													)
			->leftJoin('partner_relations','partner_relations.partner','partners.code')
			->leftJoin('partners AS partners2','partners2.code','partner_relations.parent')
			->leftJoin('partner_roles AS roles2','partner_relations.parent','roles2.partner')
			->join('partner_roles',function($join){ $join->on('partner_roles.partner','=','partners.code'); $join->on('partner_roles.status',"=",DB::raw('"ACTIVE"')); })
			->join('roles',function($join){ $join->on('roles.code','=','partner_roles.role'); $join->on('roles.status','=',DB::raw('"ACTIVE"')); })
			->leftJoin('partner_details','partner_details.partner','partners.code')
			->leftJoin('customer_industry','customer_industry.code','partner_details.industry')
			->whereIn("partners.status",["ACTIVE","PENDING"])->orderBy("partners.created_at","DESC");
		if($role) $ORM->where('roles.name',DB::raw('"'.$role.'"'));
		if($parent) $ORM->where('partner_relations.parent',DB::raw('"'.$parent.'"'));
			if($page === NULL) $ORM->where(DB::raw('DATEDIFF(now(),`partners`.`created_at`)'),"<=",DB::raw($items));
			else $ORM->limit($items)->offset($page*$items);
			return $ORM->get();
	}
	
	public function customer($code){
		return view("customer.detail1");
	}
	
	public function dealer($code){
		return view("dealer.detail1");
	}
	
	public function distributor($code){
		return view("distributor.detail1");
	}
	
	public function supportteam($code){
		return view("tst.detail1");
	}
	
	public function supportagent($code){
		return view("tsa.detail1");
	}
	
	public function ticket($code){
		return view("tkt.detail1");
	}
	
	public function task($code){
		return view("tsk.detail1");
	}
	
	public function regdetails(Request $request){
		$crc = new \App\Http\Controllers\CustomerRegistrationController;
		$ORM = $crc->GetCustomizedOrm($request->all())->own()->with(['Product','Edition','Customer' => function($Q){ $Q->with('ParentDetails.ParentDetails'); }]);
		if($request->period && $period = $request->period) $ORM = $ORM->where('created_at','>=',date('Y-m-d 00:00:00',( (is_numeric($period)) ? $period : strtotime('-'.$period,strtotime(date('Y-m-d 00:00:00')) ))));
		if($request->till && $till = $request->till) $ORM = $ORM->where('created_at','<',date('Y-m-d 00:00:00',( (is_numeric($till)) ? $till : strtotime('-'.$till,strtotime(date('Y-m-d 00:00:00')) ))));
		$Registrations = $ORM->get();
		return view('crd.index',compact('Registrations'));
	}
	
	public function listtickets($team, $status, Request $request){
		$period = ($request->period)?:false; $Title = ($period) ? 'From: '.date('d/M/y', is_numeric($period) ? $period : strtotime('-'.$period,strtotime(date('Y-m-d 00:00:00'))) ) : strtoupper($status);
		$ORM = \App\Models\Ticket::with(['Team','Customer','Product','Edition','CreatedBy'])->whereHas('Team',function($Q)use($team){ $Q->where('team',$team); });
		if($period) $ORM = $ORM->where('created_at', '>=', date('Y-m-d 00:00:00',( is_numeric($period) ? $period : strtotime('-'.$period,strtotime(date('Y-m-d 00:00:00'))) )));
		if($request->till && $till = $request->till) $ORM = $ORM->where('created_at', '<', date('Y-m-d 00:00:00',( is_numeric($till) ? $till : strtotime('-'.$till,strtotime(date('Y-m-d 00:00:00'))) )));
		if(($status = strtoupper($status)) != 'TOTAL'){
			$status = ($period) ? (($status == 'CLOSED') ? ['CLOSED','COMPLETED'] : (($status == 'NEW') ? ['NEW','OPENED'] : (($status == 'REOPENED') ? ['REOPENED','RECREATED'] : [$status]) )) : [$status];
			$ORM = $ORM->whereHas('Cstatus',function($Q)use($status){ $Q->whereIn('status',$status); });
		}
		$Data = $ORM->paginate(100);
		return view('tkt.list1',compact('Title','Data'));
	}
	
	public function DistributorTransaction($code){
		$Distributor = \App\Models\Distributor::whereCode($code)->with('Transactions')->first();
		return view('distributor.transactions',compact('Distributor'));
	}
	
	
	
	private function getPartnerDetails($code,$isCustomer = false){
		$Partner = Partner::with("details.city.state.country","details.industry","logins","parentDetails.details.city.state.country","parentDetails.roles","parentDetails.logins","customerProducts.editions","products.editions")->whereCode($code)->first()->toArray();
		$Products = [];
		$I = ($isCustomer) ? 'customer_products' : 'products';
		foreach($Partner[$I] as $k => $DA){
			$MED = $DA['pivot']['edition'];
			if(!isset($Products[$DA['code']])) $Products[$DA['code']] = [$DA['name'],[]];
			foreach($DA["editions"] as $l => $EA){
				if($EA['code'] == $MED) $Products[$DA['code']][1][$MED] = ($isCustomer) ? [$EA['name'],$DA['pivot']['created_at'],$DA['pivot']['registered_on']] : $EA['name'];
			}
		}
		unset($Partner['products'],$Partner['customer_products']);
		$Partner['products'] = $Products;
		return $Partner;
	}
	
	public function getCustomerProducts($items = NULL,$page = NULL){
		$IP = $this->PageItem($items,$page);
		return $this->CustomerProducts($IP[0],$IP[1])->map(function($item,$key){
			return array_values($item->toArray());
		})->values();
	}
	
	public function getPartnerProducts($partner, $items = NULL,$page = NULL){
		$IP = $this->PageItem($items,$page);
		return $this->CustomerProducts($IP[0],$IP[1],$partner)->map(function($item,$key){
			return array_values($item->toArray());
		})->values();
	}
	
	private function PageItem($items = NULL,$page = NULL,$request = NULL){
		if($request){
			$items = (Request()->__) ?: ((Request()->_) ?: $this->items);
			$page = (Request()->__) ? NULL : 0;
		} elseif(($items === NULL && $page === NULL) || ($items !== NULL && $page !== NULL) || ($items === NULL && $page !== NULL)){
			$page = ($page == 0)?0:(($page-1)?:$this->page);
			$items = ($items)?:$this->items;
		}
		return [$items,$page];
	}
	
	private function Dealers($Distributor){
		return Partner::select("partners.code")->join("partner_relations","partners.code","partner_relations.partner")->join("partner_roles","partners.code","partner_roles.partner")
			->where("partner_relations.parent",DB::raw('"'.$Distributor.'"'))
			->where("partner_roles.rolename","dealer")
			->pluck("code");
	}
	
	public function getDealersCustomers($distributor, $items = NULL,$page = NULL){
		$IP = $this->PageItem($items,$page);// return $this->Dealers($distributor);
		//return $this->CustomerProducts($IP[0],$IP[1]);
		return $this->CustomerProducts($IP[0],$IP[1],false,$this->Dealers($distributor))->map(function($item,$key){
			return array_values($item->toArray());
		})->values();
	}
	
	public function distributor_list(){
		return ["distributor"	=>	$this->AllPartners($this->items,$this->page,"distributor")->filter(function($v,$k){ return strtolower($v->parent_role) == 'company'; })->pluck("name","code")];
	}
	
	public function dealer_list($dst){
		return ["dealer"	=>	$this->AllPartners($this->items,$this->page,"dealer",$dst)->pluck("name","code"),
						"customer"	=>	$this->AllPartners($this->items,$this->page,"customer",$dst)->pluck("name","code")];
	}
	
	public function customer_list($dlr){
		return ["customer"	=>	$this->AllPartners($this->items,$this->page,"customer",$dlr)->pluck("name","code")];
	}
	
	public function regreqs(){
		$Data = $this->GetRegReqs()->toArray();
		return view("company.regreqs",compact("Data"));
	}
	
	public function getlicence($Customer, $Sequence){
		$Data = \App\Models\CustomerRegistration::whereCustomer($Customer)->whereSeqno($Sequence)->with("customer")->first()->toArray();
		return response()->download(storage_path("app/".$Data['lic_file']),str_replace(" ",".",$Data['customer']['name']).".lic");
	}
	
	public function register($Customer, $Seq){
		$Data = \App\Models\CustomerRegistration::whereCustomer($Customer)->whereSeqno($Seq)->with("customer.details.city.state.country","customer.logins","customer.industry","product","edition")->first()->toArray();
		return view("company.register",compact("Data"));
	}
	
	public function doregister($Customer, $Seq, Request $Request){
		$this->validate($Request,[
			"serialno"	=> "required|unique:customer_registrations,serialno",
			"key"	=>	"required|unique:customer_registrations,key"],
							["serialno.required"	=>	"Fill Serial No",
							"key.required"	=>	"Fill Registration Key field.",
							"serialno.unique"	=>	"Serial no entered is already in use.",
							"key.unique"	=>	"Registration key entered is already in use."
							]);
		$CS = \App\Models\CustomerRegistration::whereCustomer($Customer)->whereSeqno($Seq);
		$CS->update(array_merge($Request->only("serialno","key"),["registered_on"	=>	date("y-m-d")]));
		$CS->first()->customer()->update(["status"	=>	"ACTIVE"]);
		$Data = $CS->with("Customer.Logins","Customer.ParentDetails.ParentDetails.ParentDetails","Product","Edition")->first();
		$Distributor = \App\Models\Customer::find($Customer)->get_distributor();
		Mail::init()->queue(new CustomerRegistrationSuccess($Data))->to($Customer)->cc($Distributor)->send();
		SMS::init(new \App\Sms\RegistrationSuccessToDistributor($Data))->send($Distributor);
		SMS::init(new \App\Sms\RegistrationSuccessToCustomer($Data))->send($Data->Customer);
		(new \App\Http\Controllers\TransactionController())->TransactionConfirmedIdentifier($Data->requisition);
		return redirect()->action("CompanyController@regreqs")->with(["info"=>true,"type"=>"success","text"=>"Information submitted Successfully."]);
	}
	
	public function transactions($Distributor = NULL){
		return \App\Models\Transaction::select("updated_at","description","price","type")
			->whereDistributor($Distributor?:Auth()->guard("api")->user()->partner)->whereStatus("ACTIVE")->oldest("updated_at")->get()->map(function($item,$key){
				return array_values($item->toArray());
			});
	}
	
	public function transaction($Code){
		$Data = \App\Models\Transaction::select("code","date","description","price","currency","exchange_rate","amount","type","status")
			->whereDistributor($Code)->oldest('date')->get();
		$Partner = Partner::find($Code);
		return view("company.distributor_transactions",compact("Data","Partner"));
	}
	
	public function password(){
		return view("company.password");
	}
	
	public function changepassword(Request $Request){
		if(Hash::check($Request->old_password,$Request->user()->password)){
			$Valide = Validator::make($Request->all(),["password"	=>	"required|confirmed"],["password.required"	=> "Please provide a password.","password.confirmed"=>"New password and confirm password doesn't match."]);
			if($Valide->fails()) return redirect()->back()->withErrors($Valide);
			$Request->user()->password = Hash::make($Request->password);
			$Request->user()->save();
			return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Password changed successfully."]);
		}
		return redirect()->back()->with(["info"=>true,"type"=>"danger","text"=>"Old password is wrong.."]);
	}
	
	public function address(){
		$user = Auth()->user();
		$Partner = \App\Models\Partner::find($user->partner);
		$Data['name'] = $Partner->name;
		$Data = array_merge($Data,$Partner->Details->toArray(),['email'	=>	$Partner->Logins[0]->email,'country'	=>	$Partner->Details->City->State->Country->id]);
		$States = \App\Models\State::select('id as value','name as text')->whereCountry($Partner->Details->City->State->Country->id)->get()->toArray();
		$Cities = \App\Models\City::select('id as value','name as text')->whereState($Partner->Details->City->State->id)->get()->toArray();
		return view("company.address",compact("Data","States","Cities"));
	}
	
	public function changeaddress(Request $Request){
		$user = Auth()->user();
		$Partner = \App\Models\Partner::find($user->partner);
		if($Partner->name != $Request->name) $Partner->update(['name'	=>	$Request->name]);
		$Partner->Details->update($Request->only('address1','address2','phonecode','currency','phone','website','city','state'));
		if($Request->email != $user->email) $Partner->Logins()->whereEmail($user->email)->update(['email'	=>	$Request->email]);
		return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Details Updated Successfully"]);
	}
	
	public function sts(){
		return \App\Models\SupportTeam::pluck('name','code');
	}
	
	public function dist_st($distributor){
		return \App\Models\DistributorSupportTeam::where(['distributor'=>$distributor,'status'=>'ACTIVE'])->first()?:[];
	}
	
	public function udst(Request $request){
		$DT = $request->D; $ST = $request->S;
		$DST = new \App\Models\DistributorSupportTeam();
		$DST->where(['status'	=>	'ACTIVE','distributor'	=>	$DT])->update(['status'	=>	'INACTIVE']);
		return $DST->create(['distributor'	=>	$DT,'supportteam'	=>	$ST, 'assigned_by'	=>	$this->getAuthUser()->partner]);
	}
	
	private function getAuthUser(){
		return (Auth()->user())?:Auth()->guard('api')->user();
	}
	
	private function GetRegReqs(){
		return \App\Models\CustomerRegistration::whereNull("serialno")->whereNull("key")->whereNotNull("lic_file")
			->with("customer.parentDetails.parentDetails","product","edition")
			->orderBy("updated_at","desc")
			->get();
	}

}
