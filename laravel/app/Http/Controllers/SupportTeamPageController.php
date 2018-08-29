<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupportTeamCustomer;

use App\Libraries\Mail;

class SupportTeamPageController extends Controller
{
	
	public function list_customers(){
		$ORM = \App\Models\SupportTeamCustomer::with(['Parent1','Product'])->latest();
		if(Request()->search_text != ""){
			$st = '%'.Request()->search_text.'%';
			$ORM->where('name','like',$st)->orWhereHas('Logins',function($Q) use($st){ $Q->where('email','like',$st); })->orWhereHas('Details',function($Q) use($st){ $Q->where('phone','like',$st); });
		}
		$Data = $ORM->paginate(6);
		$Pagination = $Data->links();
		return view('stp.customers',compact('Data','Pagination'));
	}
	
	public function list_distributors(){
		$ORM = \App\Models\SupportTeamDistributors::with(['Parent1','Logins.Roles'	=>	function($Q){ $Q->select('login','rolename'); }]);
		if(Request()->search_text != ""){
			$st = '%'.Request()->search_text.'%';
			$ORM->where('name','like',$st)->orWhereHas('Logins',function($Q) use($st){ $Q->where('email','like',$st); })->orWhereHas('Details',function($Q) use($st){ $Q->where('phone','like',$st); });
		}
		$Data = $ORM->paginate(15);
		$Pagination = $Data->links();
		return view('stp.distributors',compact('Data','Pagination'));
	}
	
	public function get_distributor_products(Request $request){
		$Distributor = $request->D;
		if(!$this->isDistributorAssigned($Distributor)) return [];
		return $this->GetDistributorProducts($Distributor)->groupBy('partner');
	}
	
	public function distributor_resetlogin(Request $request){
		$Distributor = $request->D;
		if(!$this->isDistributorAssigned($Distributor)) return [];
		$Partner = \App\Models\Partner::whereCode($Distributor)->with('Logins','Parent.ParentDetails.Roles')->first();
		$Logins = $Partner->Logins[0];
		$pArr = ['id','partner','email','expiry'];
		$vArr = [$Logins->id,$Distributor,$Logins->email,strtotime("+18 Hours")];
		$Code = \App\Http\Controllers\KeyCodeController::Encode($pArr,$vArr);
		Mail::init()->queue(new \App\Mail\DistributorLoginReset($Partner,$Code))->send($Partner);
		return [$Partner->code, $Partner->name, $Partner->Logins[0]->email];
	}
	
	public function customer_resetlogin(Request $request){
		$Customer = $request->C;
		if(!$this->isCustomerAssigned($Customer)) return [];
		$Partner = \App\Models\Partner::whereCode($Customer)->with('Logins','Parent.ParentDetails.Roles')->first();
		$Logins = $Partner->Logins[0];
		$pArr = ['id','partner','email','expiry'];
		$vArr = [$Logins->id,$Customer,$Logins->email,strtotime("+18 Hours")];
		$Code = \App\Http\Controllers\KeyCodeController::Encode($pArr,$vArr);
		Mail::init()->queue(new \App\Mail\CustomerLoginReset($Partner,$Code))->send($Partner);
		return [$Partner->code, $Partner->name, $Partner->Logins[0]->email];
	}
	
	private function GetDistributorProducts($Distributor){
		$PEP = new \App\Models\ProductEditionPackage();
		return \App\Models\PartnerProduct::where(['partner'=>$Distributor,'status'=>'ACTIVE'])->select(['partner','product','edition'])->with([
			'Product'	=>	function($Q){ $Q->select(['code','name']); },
			'Edition'	=>	function($Q){ $Q->select(['code','name']); },
		])->get()->map(function($PP,$key) use($PEP){
			$PP->packages = $PEP->where(['product'=>$PP->Product->code,'edition'=>$PP->Edition->code])->select('package')->with([
				'Package'	=>	function($Q){ $Q->select(['code','name','type']); },
			])->get();
			return $PP;
		});
	}
	
	public function get_distributor_customers_email(Request $request){
		$Distributor = $request->D; if(!$this->isDistributorAssigned($Distributor)) return ['as'];
		$AllCustomers = $this->GetDistributorAllCustomersCode($Distributor);
		return $this->GetCustomerNameToEmail($AllCustomers);
	}
	
	private function isDistributorAssigned($D){
		return in_array($D,\App\Models\SupportTeamDistributors::pluck('code')->toArray());
	}
	
	private function isCustomerAssigned($C){
		return in_array($C,\App\Models\SupportTeamCustomer::pluck('code')->toArray());
	}
	
	private function getDistributorDealersCodes($D){
		return \App\Models\PartnerRelation::where('parent',$D)->with(['Partnerroles'])->whereHas('Partnerroles',function($Q){
			$Q->where('rolename','dealer');
		})->pluck('partner')->toArray();
	}
	
	private function getDistributorCustomersCodes($D){
		return \App\Models\PartnerRelation::where('parent',$D)->with(['Partnerroles'])->whereHas('Partnerroles',function($Q){
			$Q->where('rolename','customer');
		})->pluck('partner')->toArray();
	}
	
	private function getDealersCustomersCodes($Ds){
		return \App\Models\PartnerRelation::whereIn('parent',$Ds)->pluck('partner')->toArray();
	}
	
	public function send_product_information(Request $request){
		
		$SND = $request->SND;
		if($SND != "Customer" && $SND != "Guest") $CUS = $SND;
		if($SND == 'Guest') $CUS = $request->GE;
		if($SND == 'Customer') $CUS = $request->DC;
		if(empty($CUS)) return ['No customer to send information'];

		$PRD = $request->PRD; $EDN = $request->EDN; $PKG = $request->PKG;
		$ORM = \App\Models\Product::whereCode($PRD);
		
		$ORM -> with(['Editions'	=>	function($Q) use($PRD, $EDN, $PKG) {
			$Q->wherePivot('product', $PRD)->oldest('products_editions.level');
			if($EDN != "*") { $Q->wherePivot('edition',$EDN)->with(['Packages'	=>	function ($Q) use ($PRD, $PKG){
				$Q->wherePivot('product',$PRD)->whereType('Onetime');
				if($PKG != "*") $Q->wherePivot('package',$PKG);
			}]); } else {
				$Q->with(['Packages'	=>	function($Q) use ($PRD, $PKG){
					$Q->wherePivot('product',$PRD)->whereType('Onetime');
					if($PKG != "*") $Q->wherePivot('package',$PKG);
				}]);
			}
		}]);
		
		if($ORM->get()->isEmpty()) return ['No product information to send.'];
		
		$Data = $ORM->first();
		Mail::init()->queue(new \App\Mail\ProductDownloadLinks($Data, $CUS))->send($CUS);
		
		return $Data;
	}
	
	public function get_latest_package_version(Request $request){
		$PRD = $request->PRD; $EDN = $request->EDN; $PKG = $request->PKG;
		return \App\Models\PackageVersion::select('version_numeric','build_date','change_log','product','edition','package')->where(['product'	=>	$PRD, 'edition'	=>	$EDN, 'package'	=> $PKG, 'status'	=>	'approved'])->latest('version_sequence')->first();
	}
	
	private function GetDistributorAllCustomersCode($Distributor){
		$Dealers = $this->getDistributorDealersCodes($Distributor);
		$Customers = $this->getDistributorCustomersCodes($Distributor);
		return array_merge($this->getDealersCustomersCodes($Dealers),$Customers);
	}
	
	private function GetCustomerNameToEmail($Customer){
		$ORM = \App\Models\PartnerLogin::with(['Partner'	=>	function($Q){ $Q->select(['code','name']); }]);
		if(is_array($Customer)) $ORM->whereIn('partner',$Customer);
		else $ORM->where('partner',$Customer);
		return $ORM->get(['partner','email'])->pluck('Partner.name','email');
	}
	
	public function get_dist_prod_edit_cust(Request $request){
		$Dist = $request->D; $Prd = $request->P; $Edn = $request->E;
		if(!$this->isDistributorAssigned($Dist)) return ['Distributor not assigned.'];
		$Customers = $this->GetDistributorAllCustomersCode($Dist);
		$PEC = \App\Models\CustomerRegistration::where(['product'	=>	$Prd, 'edition'	=>	$Edn])->whereIn('customer',$Customers)->get(['customer'])->pluck('customer')->toArray();
		return $this->GetCustomerNameToEmail($PEC);
	}
	
	public function send_product_update(Request $request){
		
		$SND = $request->SND;
		if($SND != "Customer") $CUS = $SND;
		if($SND == 'Customer') $CUS = $request->DPE;
		if(empty($CUS)) return ['No customer mentioned to send updates.'];

		$Data = \App\Models\CustomerRegistration::where(["product"	=>	$request->PRD, "edition"	=>	$request->EDN])
			->with("Product","Edition")
			->get();
		
		if($Data->isEmpty()) return ['No product information to send.'];
		
		$Version = $request->VER;
		$Package = $request->PKG;
		
		return Mail::init()->queue(new \App\Mail\NewProductUpdate2($Data,$Version,$Package,$CUS))->send($CUS);
		
	}
	
	public function edit_distributor($Code){
		if(!$this->isDistributorAssigned($Code)) return redirect()->route('roledenied');
		$Partner = \App\Models\Distributor::whereCode($Code)->first(); $City = $Partner->Details->city;
		$States = ($City) ? \DB::table("states")->select("id","name")->whereCountry($Partner->Details->City->State->country)->get() : [];;
		$Cities = ($City) ? \DB::table("cities")->select("id","name")->whereState($Partner->Details->City->state)->get() : [];;
		return view('stp.distributor_edit',compact('Partner','States','Cities'));
	}
	
	public function update_distributor($Code, Request $Request){
		$Partner = \App\Models\Distributor::whereCode($Code)->with(['Countries','Logins.Roles'])->first();
		$DistEmail = $this->getDistributorRoleEmail($Partner);
		if($DistEmail != $Request->email) {
			$ES = $Partner->Logins()->whereEmail($DistEmail)->first();
			$ES->email = $Request->email; $ES->save();
		}
		$Partner->details->fill($Request->except("submit",'name','email','country'));
		if($Partner->name != $Request->name) $Partner->name = $Request->name;
		if($Partner->push()) return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Details updated successfully.."]);
		return redirect()->back()->with(["info"=>true,"type"=>"danger","text"=>"Server error, Please try again later."]);		
	}
	
	public function edit_customer($Code){
		if(!$this->isCustomerAssigned($Code)) return redirect()->back()->with(["info"=>true,"type"=>"danger","text"=>"You are not authorized to edit this customer."]);
		$Partner = \App\Models\Customer::whereCode($Code)->first(); $City = $Partner->Details->city;
		$States = ($City) ? \DB::table("states")->select("id","name")->whereCountry($Partner->Details->City->State->country)->get() : [];
		$Cities = ($City) ? \DB::table("cities")->select("id","name")->whereState($Partner->Details->City->state)->get() : [];
		//return compact('Partner','States','Cities');
		return view('stp.customer_edit',compact('Partner','States','Cities'));
	}
	
	public function update_customer($Code, Request $Request){
		$Partner = \App\Models\Customer::whereCode($Code)->with(['Logins.Roles'])->first();
		$CustEmail = $this->getCustomerRoleEmail($Partner);
		if($CustEmail != $Request->email) {
			$ES = $Partner->Logins()->whereEmail($CustEmail)->first();
			$ES->email = $Request->email; $ES->save();
		}
		$Partner->details->fill($Request->except("submit",'name','email','country'));
		if($Partner->name != $Request->name) $Partner->name = $Request->name;
		if($Partner->push()) return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Details updated successfully.."]);
		return redirect()->back()->with(["info"=>true,"type"=>"danger","text"=>"Server error, Please try again later."]);		
	}
	
	private function getDistributorRoleEmail($Partner){
		foreach($Partner->Logins->toArray() as $LAry){
			if(in_array('distributor',array_column($LAry['roles'],'rolename'))){
				return $LAry['email'];
			}
		}
	}
	
	private function getCustomerRoleEmail($Partner){
		foreach($Partner->Logins->toArray() as $LAry){
			if(in_array('customer',array_column($LAry['roles'],'rolename'))){
				return $LAry['email'];
			}
		}
	}
	
	public function get_prod_info(){
		return \App\Models\ProductEditionPackage::with(['Product'	=>	function($Q){ $Q->select('code','name'); }, 'Edition'	=>	function($Q){ $Q->select('code','name'); }, 'Package'	=>	function($Q){ $Q->select('code','name','type'); } ])->has('Product')->has('Edition')->has('Package')->get(['product','edition','package'])->groupBy('product')->transform(function($item, $key){ return $item->groupBy('edition')->transform(function($item, $key){ return $item->groupBy(function($item, $key){ return $item->Package->type; }); }); });
	}
	
	public function get_presale_dates(Request $Request){
		$CUS = $Request->CUS; $SEQ = $Request->SEQ;
		if(!$this->isCustomerAssigned($CUS)) return ['Customer is not assigned.'];
		return \App\Models\CustomerRegistration::where(['customer'	=>	$CUS, 'seqno'	=>	$SEQ])->select('customer','product','edition','registered_on','presale_enddate','presale_extended_to')->first();
	}
	
	public function update_presale_dates(Request $Request){
		$CUS = $Request->CUS; $SEQ = $Request->SEQ; $SD = $Request->SD; $ED = $Request->ED; $EX = $Request->EX;
		if(!$this->isCustomerAssigned($CUS)) return ['Customer is not assigned.'];
		$ORM = \App\Models\CustomerRegistration::where(['customer'	=>	$CUS, 'seqno'	=>	$SEQ]);
		$CR = $ORM->first();
		if((!$CR->presale_enddate && $ED) || ($ED && $CR->presale_enddate != $ED)) $ORM->update(['presale_enddate'	=>	$ED]);
		if((!$CR->presale_extended_to && $EX) || ($EX && $CR->presale_extended_to != $EX)) { $ORM->update(['presale_extended_to'	=>	$EX,	'presale_extended_by'	=>	$this->getAuthUser()->partner]); }
		return ['Updated'];
	}
	
	private function getAuthUser(){
		return (Auth()->user())?:(Auth()->guard("api")->user());
	}
	
	public function new_customer(){
		return view('stp.customer_new');
	}
	
	public function get_dist_countries(Request $Request){
		return \App\Models\Distributor::find($Request->D)->Countries()->get(['name','phonecode','currency'])->mapWithKeys(function($item){ return [$item->pivot->country => $item]; });//->groupBy(function($item){ return $item->pivot->country; });
	}
	
	public function get_dist_dealers(Request $Request){
		$Dist = $Request->D;
		return \App\Models\Dealer::with('Parent1')->whereHas('Parent1',function($Q)use($Dist){ $Q->where('parent',$Dist); })->pluck('name','code');
	}
	
	public function add_customer(Request $Request){
		$Rules = $this->NewCustomerValidation();
		$Validate = \Validator::make($Request->all(),$Rules[0],$Rules[1]);
		if($Validate->fails()) return redirect()->back()->withInput()->withErrors($Validate);
		$created_by = $this->getAuthUser()->partner;
		$Partner = new \App\Models\Partner(); $PartnerCode = $Partner->CustomerNextCode(); $Partner = $Partner::create(['code'	=>	$PartnerCode,'name'	=>	$Request->name, 'created_by'	=>	$created_by]);
		$PD = new \App\Models\PartnerDetails(); $DetailCode = $PD->NextCode();
		$Partner->details()->create(array_merge(["code"=>$DetailCode], $Request->only("currency","phonecode","phone")));
		$Logins = $Partner->logins()->create(array_merge(compact("created_by"),$Request->only("email")));
		$Partner->countries()->attach($Request->country);
		$Partner->parent()->create(["parent"=>(($Request->dealer)?:$Request->distributor)]);
		$Register = $Partner->register()->create(["product"=>$Request->product,"edition"=>$Request->edition,"created_by"=>$created_by,"presale_enddate"=>date("Y-m-d",strtotime($Request->presale_enddate))]);
		\App\Models\CustomerSupportTeam::create(['customer'	=>	$Partner->code, 'supportteam'	=>	(session()->get('_rolename') == 'supportteam')?$created_by:$this->GetSupportTeam($created_by), 'assigned_by'	=>	$created_by, 'product'	=>	$Request->product, 'edition'	=>	$Request->edition]);
		$RoleCode = \App\Models\Role::whereName("customer")->first()->code;
		$login = $Logins->id; $created_at = date('Y-m-d H:i:s'); $updated_at = date('Y-m-d H:i:s');
		$Partner->roles()->attach($RoleCode,compact("login","created_by","created_at","updated_at"));
		session()->flash("info",true); session()->flash("type","success"); session()->flash("text","Customer, " . $Request->name . " added Successfully");
		Mail::init()->queue(new \App\Mail\NewCustomer($PartnerCode,$Request->email,$login))->send($Request->email);
		return redirect()->back();
	}
	
	private function NewCustomerValidation(){
		$Rules = [
			"name"						=>	"required|unique:partners,name",
			"email"						=>	"required|email|unique:partner_logins,email",
			"product"					=>	"required",
			"edition"					=>	"required",
			"presale_enddate"	=>	"required|after:yesterday",
		];
		$Messages = [
			"name.required"			=>	"The Name field cannot be empty.",
			"name.unique"				=>	"The Name is already taken, Try a new name.",
			"email.required"		=>	"Email is Mandatory, Please fill.",
			"email.email"				=>	"Email doesn't seems to be a valid one",
			"email.unique"			=>	"Email is already in use.",
			"product.required"	=>	"Please mention the product.",
			"edition.required"	=>	"Please mention the Edition.",
			"presaleend.required"	=>	"Presale end date is a required field.",
			"presaleend.after"	=>	"Presale end date should be somewhat future date.",
		];
		return [$Rules,$Messages];
	}
	
	public function product_interact(){
		$Data = \App\Models\Product::select('code','name','description_public','private')->whereActive('1')->with(['Editions'	=>	function($Q){ $Q->select('code','name','private')->whereActive('1'); }])->has('Editions')->get();
		$this->tmp = new \App\Models\PackageVersion();
		$Product = $Data->groupBy('code')->map(function($item){
			$Data = $item->first(); $PRD = $Data->code;
			return ['name'	=>	$Data->name, 'private'	=> $Data->private, 'description'	=>	$Data->description_public, 'editions'	=>	$Data->Editions->mapWithKeys(function($item)use($PRD){
				$edition = $item->code;
				return [$edition	=>	['name'	=>	$item->name, 'private'	=>	$item->private, 'description'	=>	$item->pivot->description, 'version'	=>	$this->GetLatestVersion($PRD,$edition)]];
			})];
		});
		return view('stp.product_interact',compact('Product'));
	}
	
	protected $tmp;
	private function GetLatestVersion($P,$E){
		$ORM = $this->tmp->where(['status'	=>	"APPROVED", 'product'	=>	$P, 'edition'	=>	$E])->with('Package')->whereHas('Package',function($Q){ $Q->whereType('Update'); })->latest('version_sequence');
		return ($ORM->get()->isNotEmpty())?$ORM->first()->version_numeric:'';
	}
	
	public function get_packages(Request $request){
		return \App\Models\ProductEditionPackage::where(['product'=>$request->PID,'edition'=>$request->EID])->with(['Packages'	=>	function($Q){
			$Q->select('code','name','type');
		}])->select('package')->get();
	}
	
	public function get_product_and_perm_cats(Request $request){
		$Cus = $request->cus; $Dist = \App\Models\Customer::find($Cus)->get_distributor()->code;
		$Products = $this->getCustomerProductEdition($Cus);
		$OnDCats = $this->getOndemandCategories($Dist);
		$PreCats = $this->getPresaleCategories($Dist);
		$Categs = $OnDCats->merge($PreCats);
		$CurCats = $this->getDemandedCategories($Cus,$Products);
		return ['products' => $Products, 'categories' => $Categs, 'current' => $CurCats];
	}
	
	private function getCustomerProductEdition($Cus){
		return \App\Models\CustomerRegistration::where(['customer' => $Cus, 'status' => 'ACTIVE'])->with(['Product','Edition'])->get()->mapWithKeys(function($item){
		    $PRD = $item->Product->name.' '.$item->Edition->name.' Edition';
		    if($item->remarks) $PRD .= ' (' . $item->remarks . ')';
			return [$item->seqno => $PRD];
		})->toArray();
	}
	
	private function getOndemandCategories($Dist){
		return \App\Models\TicketCategoryMaster::withoutGlobalScope('own')->with('excludedDistributors')->where('available','onDemand')->get()->transform(function($item)use($Dist){ if($item->ExcludedDistributors->contains('distributor',$Dist)) $item->name .= ' - (Excluded)'; return $item; })->pluck('name','code');
	}
	
	private function getPresaleCategories($Dist){
		return \App\Models\TicketCategoryMaster::withoutGlobalScope('own')->with('excludedDistributors')->where('available','isPresale')->get()->transform(function($item)use($Dist){ if($item->ExcludedDistributors->contains('distributor',$Dist)) $item->name .= ' - (Excluded)'; return $item; })->pluck('name','code');
	}
	
	private function getDemandedCategories($C, $P){
		$Seqs = array_keys($P); $TC = new \App\Models\TicketCategoryMaster; $Permits = [];
		foreach($Seqs as $seq){
			$Permits[$seq] = $TC->ondemand_get($C,$seq);
		}
		return $Permits;
	}
	
	public function update_product_perm_cats(Request $request){
		$FunPart = ['allow' => 'add', 'disallow' => 'delete']; $Function = 'ondemand_' . $FunPart[$request->sta];
		$TC = new \App\Models\TicketCategoryMaster; $TC->{$Function}($request->cus, $request->seq, $request->cat);
	}
	
	public function GetSupportTeam($partner){
		$Partner = \App\Models\Partner::whereCode($partner)->with('Roles','Parent')->first();
		if($Partner->Roles->contains('name','supportteam')) return $Partner->code;
		return $this->GetSupportTeam($Partner->Parent->parent);
	}
	
	
}
