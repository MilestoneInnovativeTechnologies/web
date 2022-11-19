<?php

namespace App\Http\Controllers;

use App\Models\CustomerRegistration;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Http\Requests\NewCustomerFormRequest;

use App\Models\Partner;
use App\Models\PartnerDetails;
use App\Models\CustomerIndustry;

use DB;
use App\Libraries\Mail;
use App\Libraries\SMS;
use App\Mail\NewCustomer;
use App\Mail\NewRegRequest;
use App\Mail\CustomerLoginReset;


class CustomerController extends Controller
{

	static function ToValArray($Obj,$Names = NULL){
		$Obj = $Obj->toArray();
		if(empty($Obj)) return [];
		$Names = ($Names)?: array_keys( (array) $Obj[0]);
		return [array_map(function($Item) use($Names){
			$MyArray = [];
			foreach($Names as $name) $MyArray[] = $Item[$name];
			return $MyArray;
		},$Obj),$Names];
	}

	public function add(){
		$Partner = new Partner(); $Code = $Partner->CustomerNextCode();
		return view("customer.new",compact("Code"));
	}

	public function create(NewCustomerFormRequest $Request){
		$PartnerCode = $this->NewPartner($Request); $created_by = $Request->user()->partner; $created_at = date("Y-m-d H:i:s"); $updated_at = date("Y-m-d H:i:s");
		$Partner = Partner::find($PartnerCode);
		$PD = new PartnerDetails; $DetailCode = $PD->NextCode(); $industry = $this->ProcessCustomerIndustry($Request);
		$Partner->details()->create(array_merge(["code"=>$DetailCode, "industry"=>$industry], $Request->only("address1","address2","city","state","currency","phonecode","phone","website")));
		$password = bcrypt(str_random(8));
		$Partner->logins()->create(array_merge(compact("password","created_by"),$Request->only("email")));
		$Partner->countries()->attach($Request->country);
		$Partner->parent()->create(["parent"=>(($Request->dealer)?:$created_by)]);
		$Register = $Partner->register()->create(["product"=>$Request->product,"edition"=>$Request->edition,"created_by"=>$created_by,"presale_enddate"=>date("Y-m-d",strtotime($Request->presaleend))]);
		$this->addCustomerSupportTeam($Register,$Partner);
		$RoleCode = Role::whereName("customer")->first()->code;
		$login = $Partner->logins()->whereEmail($Request->email)->pluck("id")[0];
		$Partner->roles()->attach($RoleCode,compact("login","created_by","created_at","updated_at"));
		//$Partner->presale()->create(["startdate"=>date("Y-m-d"),"enddate"=>date("Y-m-d",strtotime($Request->presaleend)),"created_by"=>$created_by]);
		session()->flash("info",true); session()->flash("type","success"); session()->flash("text","Customer, " . $Request->name . " added Successfully");
		Mail::init()->queue(new NewCustomer($PartnerCode,$Request->email,$login))->to($Partner)->send();
		return redirect()->route("dashboard");
	}

	private function NewPartner($Request){
		$Partner = new Partner(); $PartnerCode = $Partner->CustomerNextCode();
		$Partner->create(["code"	=>	$PartnerCode, "name"	=> $Request->name, "status"	=>	"PENDING", "created_by"	=>	$Request->user()->partner]);
		return $PartnerCode;
	}

	private function NewPartnerDetails($PartnerCode, $Request){
		$industry = $this->ProcessCustomerIndustry($Request);
		$PD = new PartnerDetails; $DetailCode = $PD->NextCode();
		$PDCreateArray = array_merge(["code"=>$DetailCode, "partner"=>$PartnerCode, "industry"=>$industry], $Request->only("address1","address2","city","state","currency","phonecode","phone","website"));
		$PD->create($PDCreateArray);
	}

	private function ProcessCustomerIndustry($Request){
		$Code = $Request->industry;
		$CI = new CustomerIndustry();
		if($Code == "-1"){
			if($Request->new_industry){
				if($CI->whereName($Request->new_industry)->get()->isEmpty()){ $code = $CI->NextCode(); $name = $Request->new_industry; $created_by = $Request->user()->partner; $CI->create(compact("code","name","created_by")); }
				else { $code = $CI->whereName($Request->new_industry)->first()->code; }
				return $code;
			}
			return NULL;
		}
		return ($CI->whereCode($Code)->get()->isEmpty()) ? NULL : $Code;
	}


	public function uniquenamecheck($name){
		return ["unique" => Partner::whereName($name)->get()->isEmpty(),"name"	=>	$name];
	}

	public function industries(){
		return \App\Models\CustomerIndustry::get();
	}

	public function uniqueemailcheck($email){
		return ["unique" => \App\Models\User::whereEmail($email)->get()->isEmpty(),"email"	=>	$email];
	}

	public function products(){
		$user = Auth()->guard("api")->user()->partner;
		return \App\Models\PartnerProduct::select("product","edition")->where(["partner"=>$user])->with("products")->get()->groupBy("product");
	}

	public function editions($product){
		$user = Auth()->guard("api")->user()->partner;
		return \App\Models\PartnerProduct::select("edition")->where(["partner"=>$user,"product"=>$product])->with("editions")->get()->groupBy("edition");
	}

	public function dealers(){
		$RWC = Partner::find(Auth()->guard("api")->user()->partner)->RoleWithChildren();
		return Partner::select("code","name")->whereIn("code",array_column($RWC["dealer"],0))->whereStatus("ACTIVE")->pluck("name","code");
	}

	public function index(){
		return view("customer.index");
	}

	public function lists($page, $items){
		if(Auth()->guard("api")->user()->Roles->contains('name','company')){
			return $this->GetRegDetails(false, ($page-1 > 0)?:0);
		} else {
			$Partner = Partner::whereCode(Auth()->guard("api")->user()->partner)->first();
			$AllChilds = $Partner->AllChildren();
			return $this->GetRegDetails($AllChilds, ($page-1 > 0)?:0);
		}
	}

	private function GetRegDetails($Partners, $page=0, $items=40){
		$ORM = \App\Models\CustomerRegistration::select("customer","seqno","product","edition","created_at AS added_on","registered_on","presale_enddate","presale_extended_to","serialno","key")
			->with(["customer"	=>	function($Q){
				$Q->select("code","name")->with(["logins"	=>	function($R){
					$R->select("partner","email");
				}]);
			},"product"	=>	function($Q){
				$Q->select("code","name");
			},"edition"	=>	function($Q){
				$Q->select("code","name");
			},"parent.roles"	=>	function($Q){
				$Q->select("roles.code","roles.name");
			}])->latest()->limit($items)->offset($page*$items);
		if($Partners !== false) $ORM->whereIn("customer",$Partners);
		return $ORM->get();
	}

	public function show(Partner $Customer){
		//$With = ["details.city.state.country"];//,"logins","register.product","register.edition","register.extender"
		//if(session()->get("_rolename") == "dealer") $With[] = "parentDetails.roles";
		$Data = $Customer;//->with($With)->whereCode($Customer->code)->first();
		return view("customer.show",compact("Data"));
	}

	public function presale(Partner $Customer){
		//return $Customer;
		//$Data = $Customer->with("register.product","register.edition")->whereCode($Customer->code)->first();
		//return ($Data);
		$Data = $Customer;
		return view("customer.presale",compact("Data"));
	}

	public function storepresale(Partner $Customer, Request $request){
		$E = $request->E;
		$X = $request->X;
		foreach($Customer->register as $CObj){
			$ended = date('Y-m-d',strtotime($E[$CObj->seqno]));
			\App\Models\CustomerRegistration::whereCustomer($Customer->code)->whereSeqno($CObj->seqno)->where("presale_enddate","<>",$ended)->update(["presale_enddate"=>$ended]);
			$extended = ($X[$CObj->seqno]) ? date('Y-m-d',strtotime($X[$CObj->seqno])) : NULL;
			$EO = \App\Models\CustomerRegistration::whereCustomer($Customer->code)->whereSeqno($CObj->seqno);
			if($EO->first()->presale_extended_to != $extended) $EO->update(["presale_extended_to"=>$extended,"presale_extended_by"=>$request->user()->partner]);
		}
		return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Presale dates updated successfully."]);
	}

	public function register($Customer, $Seqno){
		$Data = Partner::with(["details.city.state.country","register"	=>	function($Q) use($Seqno){
			$Q->where("seqno",$Seqno);
		},"register.product"	=>	function($Q){
			$Q->select("code","name");
		},"register.edition"	=>	function($Q){
			$Q->select("code","name");
		},"logins"	=>	function($Q){
			$Q->select("partner","email");
		}])->whereCode($Customer)->first()->toArray();
		return view("customer.register",compact("Data","Seqno"));
	}

	public function edit($Customer){
		$Customer = \App\Models\Customer::whereCode($Customer)->with('Parent1.ParentCountries')->first();
		//return $Customer->Parent1->ParentCountries->mapWithKeys(function($item){ return [$item->Country->id => $item->Country->name]; })->toArray();
		$Countries = Partner::find(Request()->user()->partner)->countries;
		$Industries = $this->industries();
		return view("customer.edit",compact("Customer","Countries","Industries"));
	}

	public function update(Partner $Customer, Request $Request){
		if($Request->email != "" && filter_var($Request->email, FILTER_VALIDATE_EMAIL) == $Request->email) {
			$Login = $Customer->whereCode($Customer->code)->with('Logins.Roles')->whereHas('Logins.Roles',function($Q){ $Q->whereRolename('customer'); })->first();
			if($Login && $Request->email != $Login->Logins->first()->email){
				$ES = $Login->Logins->first()->update(['email'	=>	$Request->email]);
				if(!$ES) redirect()->back()->with(["info"=>true,"type"=>"error","text"=>"Error in updating Email Address."]);
			}
		}
		$Request->merge(["industry"=>$this->ProcessCustomerIndustry($Request)]);
		if($Customer->name != $Request->name) { $Customer->update(["name"	=>	$Request->name]); }
		$Customer->Details->update($Request->only("address1","address2","city","state","industry","phone","phonecode","website"));
		$OldCntry = ($Customer->Details->city) ? $Customer->Details->City->State->Country->id : NULL; $NewCntry = $Request->country;
		if($OldCntry != $NewCntry) {
			$this->validate($Request, [
					"country"	=>	"required|exists:partner_countries,country,partner," . ($Customer->Parent1->parent)
			],["country.required"	=>	"Country field is mandatory", "country.exists"	=>	"You are not authorized to assign the country selected."]);
			if($OldCntry !== NULL) $Customer->countries()->detach($OldCntry);
			$Customer->countries()->attach($NewCntry);
		}
		$this->checkCustomerSupportTeam($Customer->Register);
		return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Details updated successfully."]);
	}

	public function regrequest($Customer, $Seqno, Request $Request){
		$Data = (array) simplexml_load_string(\Storage::get($FilePath = $Request->file("licence")->store("upload/licence")));
		$CR = \App\Models\CustomerRegistration::whereCustomer($Customer)->whereSeqno($Seqno);
		$UA = ["lic_file"	=>	$FilePath, "version"	=>	$Data['SoftwareVersion'], "database"	=>	$Data['DatabaseName'], "requisition"	=>	$this->GetNewRequisition()];
		$CR->update($UA); $Company = \App\Models\Company::first(); $Customer = \App\Models\Customer::find($Customer); $Distributor = $Customer->get_distributor();
		Mail::init()->queue(new NewRegRequest($CR, array_merge($Data,$UA), $Request->user(), $Company))->to($Company)->cc($Distributor)->send();
		SMS::init(new \App\Sms\RegistrationRequesToAuthor($Customer))->gateway('SMPPSMS')->send(\App\Models\Partner::find('thahir'));
		(new \App\Http\Controllers\TransactionController())->CustomerRegistrationInitialized($CR->with("Customer.ParentDetails","product","edition")->first());
		return redirect()->route("customer.index")->with(["info"=>true,"type"=>"success","text"=>"Registration Request Submitted Successfully."]);
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

	public function resetlogin($Customer){
		$Partner = Partner::whereCode($Customer)->with('Logins','Parent.ParentDetails.Roles')->first();
		$Logins = $Partner->Logins[0];
		$pArr = ['id','partner','email','expiry'];
		$vArr = [$Logins->id,$Customer,$Logins->email,strtotime("+18 Hours")];
		$Code = (new \App\Http\Controllers\KeyCodeController())->KeyEncode($pArr,$vArr);
		Mail::init()->queue(new CustomerLoginReset($Partner,$Code))->to($Partner)->send();
		return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Login reset instructions have been mailed to, ".$Partner->Logins[0]->email]);
		//return [$Partner->code, $Partner->name, $Partner->Logins[0]->email];
	}

	public function productregister($Customer){
		$Regs = \App\Models\CustomerRegistration::where(function($Q)use($Customer){ $Q->where('customer',$Customer)->whereNull('registered_on'); })->get();
		if($Regs->isEmpty()) return redirect()->back()->with(["info"=>true,"type"=>"warning","text"=>"You have no products to register."]);
		if($Regs->count() == 1) return redirect()->action('CustomerController@register',['Customer' => $Customer, 'Seqno' => $Regs[0]->seqno]);
		return view('customer.productregister',compact('Regs'));
	}

	private function addCustomerSupportTeam($Reg){
		$Partner = Partner::find($Reg->customer);
		$Distributor = $this->getDistributor($Partner);
		$SupportTeam = $this->getSupportteamOfDistributor($Distributor);
		if(!$SupportTeam){
			$DST = $this->getDefaultSupportTeam();
			if($DST) {
				$this->addDistributorSupportteam($Distributor, $DST);
				$SupportTeam = $DST;
			}
		}
		if($SupportTeam) {
			$Cust = $Reg->customer; $Prod = $Reg->product; $Edit = $Reg->edition; $CST = new \App\Models\CustomerSupportTeam();
			$Cond = ['customer'	=>	$Cust, 'product'	=>	$Prod, 'edition'	=>	$Edit]; $Data = ['supportteam'	=>	$SupportTeam->code, 'assigned_by'	=>	$this->getAuthUser()->partner];
			if($CST->where($Cond)->get()->isNotEmpty()) return $CST->where($Cond)->update($Data);
			return $CST->create(array_merge($Cond,$Data));
		}
	}

	private function checkCustomerSupportTeam($Regs){
		$Regs->each(function($Reg, $Key){
			if(!$this->hasRegCustomerSupportTeamAssigned($Reg))
				$this->addCustomerSupportTeam($Reg);
		});
	}

	private function hasRegCustomerSupportTeamAssigned($Reg){
		$Cust = $Reg->customer; $Prod = $Reg->product; $Edit = $Reg->edition;
		$CST = new \App\Models\CustomerSupportTeam();
		$Cond = ['customer'	=>	$Cust, 'product'	=>	$Prod, 'edition'	=>	$Edit];
		return $CST->where($Cond)->get()->isNotEmpty();
	}

	public function get_distributor_of_partner($Partner){
		return $this->getDistributor(Partner::whereCode($Partner)->with('Parent')->first());
	}

	private function getDistributor($Partner){
		$Parent = $this->getParent($Partner);
		if($this->PartnerHasRole($Parent,'distributor')) return $Parent;
		if($this->PartnerHasRole($Parent,'company') || $this->PartnerHasRole($Parent,'COMPANY')) return null;
		return $this->getDistributor($Parent);
	}

	private function getParent($Partner){
		return Partner::find($Partner->Parent->parent);
	}

	private function PartnerHasRole($Partner,$Role){
		$Roles = $this->getRolesCollection($Partner);
		return $Roles->contains($Role);
	}

	private function getRolesCollection($Partner){
		return $Partner->Roles->pluck('name');
	}

	private function getSupportteamOfDistributor($Dist){
		$DST = \App\Models\DistributorSupportTeam::whereDistributor($Dist->code)->get();
		if($DST->isNotEmpty()) return $DST->first()->Team;
		return null;
	}

	private function getDefaultSupportTeam(){
		$DST = \App\Models\DefaultSupportTeam::all();
		if($DST->isNotEmpty()) return $DST->first()->Partner;
		return null;
	}

	private function addDistributorSupportteam($Distributor, $SupportTeam){
		$DST = new \App\Models\DistributorSupportTeam();
		$DST->whereDistributor($Distributor->code)->update(['status'=>'INACTIVE']) ;
		$DST->create(['distributor'	=>	$Distributor->code,	'supportteam'	=>	$SupportTeam->code, 'assigned_by'	=>	$this->getAuthUser()->partner]);
	}

	private function getAuthUser(){
		return (Auth()->user())?:Auth()->guard('api')->user();
	}

	public function changeproduct($Customer, $Sequence){
		if(!$this->isCustomerOfPartner($Customer,$this->getAuthUser()->partner)) return redirect()->back()->with(["info"=>true,"type"=>"danger","text"=>"Customer not belogs to you."]);
		$Customer = \App\Models\CustomerRegistration::where(['customer'	=>	$Customer, 'seqno'	=>	$Sequence])->whereNull('registered_on')->select('customer','product','edition','created_at','presale_enddate','presale_extended_to')->select('customer','product','edition')->first();
		return view('customer.changeproduct',compact('Customer'));
	}

	private function isCustomerOfPartner($Customer,$Parent){
		return \App\Models\Customer::whereCode($Customer)->get()->isNotEmpty();
		/* $Partner = new \App\Models\Partner();
		$isDealer = $Partner->find($Parent)->Roles->contains('name','dealer');
		if($isDealer) return $Partner->find($Parent)->Children->contains('partner',$Customer);
		$isDistrubutorCustomer = $Partner->find($Parent)->Children->contains('partner',$Customer);
		if($isDistrubutorCustomer) return true;
		$DistDealers = $Partner->find($Parent)->Children->pluck('partner')->toArray();
		return $Partner->whereIn('code',$DistDealers)->get()->contains('code',$Customer); */
	}

	public function dochangeproduct($Customer, $Sequence, Request $Request){
		if(!$this->isCustomerOfPartner($Customer,$this->getAuthUser()->partner)) return redirect()->route('customer.index')->with(["info"=>true,"type"=>"danger","text"=>"Customer not belogs to you."]);
		$ORM = \App\Models\CustomerRegistration::where(['customer'	=>	$Customer, 'seqno'	=>	$Sequence])->whereNull('registered_on');
		$Data = $ORM->first();
		if($Data->edition == $Request->edition && $Data->product == $Request->product)  return redirect()->back()->with(["info"=>true,"type"=>"info","text"=>"No changes identified."]);
		else $ORM->update(['edition'	=>	$Request->edition, 'product'	=>	$Request->product]);
		return redirect()->route('customer.index')->with(["info"=>true,"type"=>"success","text"=>"Changes saved successfully."]);
	}

	public function tickets($Customer = null){
		$Cust = $Customer?:Auth()->guard("api")->user()->partner;
		$Tickets = \App\Models\Ticket::where('customer',$Cust)->with(['Product','Edition','Cstatus'])->paginate(100);
		return view('customer.tickets',compact('Tickets'));
	}

	public function search(Request $request){
		$like = '%'.$request->term.'%';
		return \App\Models\Customer::with('Details','Logins','Roles','ParentDetails')->where(function($Q) use($like){
			$Q->orWhere('code','like',$like)
				->orWhere('name','like',$like)
				->orWhereHas('Details',function($Q) use($like){ $Q->where('phone','like',$like); })
				->orWhereHas('Logins',function($Q) use($like){ $Q->where('email','like',$like); })
				->orWhereHas('Register',function($Q) use($like){ $Q->where('remarks','like',$like); })
				;
		})->get();
	}

	public function detail_search(Request $request){
		$ORM = new \App\Models\Customer;
		if($request->customer) $ORM = $this->ModifyORMForSearch($ORM,$request->customer);
		if($request->dealer) $ORM = $this->ModifyORMForFilter($ORM,'Dealer',$request->dealer);
		elseif($request->distributor) $ORM = $this->ModifyORMForFilter($ORM,'Distributor',$request->distributor);
		if($request->country) $ORM = $this->ModifyORMForFilter($ORM,'Country',$request->country);
		if($request->product) $ORM = $this->ModifyORMForFilter($ORM,'Product',$request->product);
		if($request->edition) $ORM = $this->ModifyORMForFilter($ORM,'Edition',$request->edition);
		return $ORM->get();
	}

	public function add_product($code){
		$Customer = \App\Models\Customer::with(['registration' => function($Q){ return $Q->with(['product','edition']); }])->find($code);
		return view('customer.add_product',compact('Customer'));
	}

	public function doadd_product($code,Request $Request){
        $Partner = Partner::find($code);
        $created_by = $Request->user()->partner; $created_at = date("Y-m-d H:i:s"); $updated_at = date("Y-m-d H:i:s");
        $product = $Request->product; $edition = $Request->edition; $remarks = $Request->remarks;
        $max = CustomerRegistration::where('customer',$code)->max('seqno'); $seqno = $max ? intval($max)+1 : 1;
        $Register = $Partner->register()->create(compact('product','edition','seqno','remarks','created_at','updated_at','created_by'));
        $this->addCustomerSupportTeam($Register);
        return redirect()->back()->with(['info' => true, 'type' => 'success', 'text' => 'Product added successfully!!']);
    }

	public function change_distributor($code){
		$Customer = \App\Models\Customer::find($code)->load('ParentDetails');
		if(session()->get('_rolename') == 'distributor') $Parents = \App\Models\Dealer::all();
		else $Parents = \App\Models\Distributor::all();
		//return $Parents;
		return view('customer.change_distributor',compact('Customer','Parents'));
	}

	public function dochange_distributor($code, Request $request){
		if(!$request->parent) return redirect()->back()->with(['info' => true, 'type' => 'danger', 'text' => 'Parent/Distributor cannot be empty.']);
		$PR = \App\Models\PartnerRelation::find($code);
		$PR->parent = $request->parent; $PR->save();
		return redirect()->back()->with(['info' => true, 'type' => 'success', 'text' => 'Distributor/Parent updated successfully.']);
	}

	private function ModifyORMForSearch($ORM,$Term){
		$like = '%'.$Term.'%';
		return $ORM->where(function($Q) use($like){
			$Q->orWhere('code','like',$like)
				->orWhere('name','like',$like)
                ->orWhereHas('Register',function($Q) use($like){ $Q->where('remarks','like',$like); })
				->orWhereHas('Details',function($Q) use($like){ $Q->where('phone','like',$like); })
				->orWhereHas('Logins',function($Q) use($like){ $Q->where('email','like',$like); })
				;
		});
	}

	private function ModifyORMForFilter($ORM,$Item,$Term){
		switch($Item){
			case 'Dealer':
				$ORM = $ORM->where(function($Q) use($Term){
					$Q->whereHas('Parent1',function($Q) use($Term){ $Q->where('parent',$Term); });
				});
				break;
			case 'Distributor':
				$ORM = $ORM->where(function($Q) use($Term){
					$Q->whereHas('Parent1',function($Q) use($Term){ $Q->where('parent',$Term)->orWhereHas('Parent1',function($Q) use($Term){ $Q->where('parent',$Term); }); });
				});
				break;
			case 'Country':
				$ORM = $ORM->where(function($Q) use($Term){
					$Q->whereHas('Details.City.State',function($Q) use($Term){ $Q->where('country',$Term); });
				});
				break;
			case 'Product':
				$ORM = $ORM->where(function($Q) use($Term){
					$Q->whereHas('registration',function($Q) use($Term){ $Q->where('product',$Term); });
				});
				break;
			case 'Edition':
				$ORM = $ORM->where(function($Q) use($Term){
					$Q->whereHas('registration',function($Q) use($Term){ $Q->where('edition',$Term); });
				});
				break;
		}
		return $ORM;
	}

}
