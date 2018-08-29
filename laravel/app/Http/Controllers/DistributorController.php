<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partner;
use Validator;
use DB;
use Hash;
use App\Libraries\Mail;
use App\Mail\NewDistributor;
use App\Mail\DistributorProductModification;
use App\Mail\DistributorCountryModification;
use App\Mail\DistributorLoginReset;

class DistributorController extends Controller
{
 		

   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("distributor.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
			$Dist = new Partner();
			$Code = $Dist->DistributorNextCode();
			list($Products,$Editions) = $this->GetProductsEditions();
      return view("distributor.create",compact("Products","Editions","Code"));
    }
	
		private function GetProductsEditions(){
			$Products = \App\Models\Product::select("code","name","private")->with(["editions" => function($q){
				$q->select("code","name","private");
			}])->whereActive(1)->get()->groupBy("code")->map(function($item, $key){
				return ($item[0]->editions->isEmpty()) ? NULL : ["name"=>$item[0]->name,"private"=>$item[0]->private,"editions"=>$item[0]->editions->pluck("private","code"),"code"=>$item[0]->code];
			})->filter();
			$Editions = \App\Models\Edition::select("code","name")->whereActive(1)->get()->pluck("name","code");
			return [$Products,$Editions];
		}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
			$RM = $this->RulesMessages(); $Rules = $RM[0]; $Messages = $RM[1];
			$Validate = Validator::make($request->all(),$Rules,$Messages);
			if($Validate->fails()) return redirect()->back()->withInput()->withErrors($Validate);
			$request->merge(["created_by"=>$request->user()->partner]);
      $Partner = Partner::create($request->all());
			if(!$Partner) return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in creating Distributor, Please try again later."]);
			if($Partner->countries()->attach($request->country) === false) { $Partner->delete(); return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in creating Distributor, (Adding country failed), Please try again later."]); }
			if(!$Partner->details()->create(array_merge(["code"=>$this->NewDetailsCode()],$request->only("address1","address2","city","state","currency","phonecode","phone","website","pricelist")))) { $Partner->delete(); return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in creating Distributor, (Adding details failed), Please try again later."]); }
			if(!$Partner->logins()->create(array_merge(["password"=>bcrypt($pwd = str_random(8)),"created_by"=>$request->user()->partner],$request->only("email")))) { $Partner->delete(); return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in creating Distributor, (Creating login failed), Please try again later."]); }
			if($Partner->products()->detach() === false) { $Partner->delete(); return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in creating Distributor, (Adding products, (Pre-processing) failed), Please try again later."]); }
			foreach($request->product as $k => $v) $Partner->products()->attach([$v	=> ["edition"	=>	$request->edition[$k],"created_by"	=>	$request->created_by,"created_at"	=>	date("Y-m-d H:i:s"),"updated_at"	=>	date("Y-m-d H:i:s")]]);
			if(!$Partner->parent()->create(["parent"=>$request->user()->partner])) { $Partner->delete(); return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in creating Distributor, (Adding relation failed), Please try again later."]); }
			$login = $Partner->logins()->whereEmail($request->email)->pluck("id")[0]; $RoleCode = \App\Models\Role::whereName("distributor")->first()->code;
			if($Partner->roles()->attach($RoleCode,["login"=>$login,"created_by"=>$request->user()->partner,"created_at"=>date("Y-m-d H:i:s"),"updated_at"=>date("Y-m-d H:i:s")]) === false) { $Partner->delete(); return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in creating Distributor, (Adding roles failed), Please try again later."]); }
			$this->AssignDefaultSupportTeam($Partner->code);
			$this->AssignContactMethods($Partner->code);
			Mail::init()->queue(new NewDistributor($request,$request->user()->partner,$login,$Partner->code))->send($Partner);
			return redirect()->route("dashboard")->with(["info"=>true,"type"=>"success","text"=>"Distributor, ".$request->name." added successfully"]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($Code)
    {
        return view("distributor.show",compact("Code"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($Code)
    {
			$Array = ["code","name","country","phonecode","currency","phone","email","pricelist","address1","address2","city","state","website"];
			$Array = array_fill_keys($Array,"Details"); $Array["email"] = "logins"; $Array["country"] = $Array["phonecode"] = $Array["currency"] = "countries"; $Array["code"] = $Array["name"] = "0"; 
			$Dist = Partner::with("details","logins","countries","products")->whereCode($Code)->first();
			$Details = [];
			foreach($Array as $key => $sub){
				if($sub == "Details")	$Details[$key] = $Dist->Details->$key;
				elseif($sub == "logins")	$Details[$key] = $Dist->logins[0]->$key;
				elseif($sub == "countries")	$Details[$key] = ($Dist->countries[0]->$key)?:$Dist->countries[0]->id;
				else $Details[$key] = $Dist->$key;
			}
			$Details["product"] = $Details["edition"] = [];
			$Dist->products->map(function($item, $key) use(&$Details){
				$Details["product"][] = $item->pivot->product;
				$Details["edition"][] = $item->pivot->edition;
			});
			$Products = \App\Models\Product::select("code","name","private")->with(["editions" => function($q){
				$q->select("code","name","private");
			}])->whereActive(1)->get()->groupBy("code")->map(function($item, $key){
				return ($item[0]->editions->isEmpty()) ? NULL : ["name"=>$item[0]->name,"private"=>$item[0]->private,"editions"=>$item[0]->editions->pluck("private","code"),"code"=>$item[0]->code];
			})->filter();
			$Editions = \App\Models\Edition::select("code","name")->whereActive(1)->get()->pluck("name","code");
			$Update = true;
			//return compact("Products","Editions","Code","Update","Details");
			return view("distributor.create",compact("Products","Editions","Code","Update","Details"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $Code)
    {
			//return $Code;
			//return $request->all();
			$RM = $this->RulesMessages(); $Rules = $RM[0]; $Messages = $RM[1];
			if($Code == $request->code) unset($Rules["code"]);
			$Partner = Partner::find($Code);
			if($Partner->logins[0]->email == $request->email) unset($Rules["email"]);
			$Validate = Validator::make($request->all(),$Rules,$Messages);
			if($Validate->fails()) return redirect()->back()->withInput()->withErrors($Validate);
			if(!$Partner->update($request->all())) return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in updating Distributor, Please try again."]);
			if(!$Partner->details()->update(array_merge(["code"=>$request->code],$request->only("address1","address2","city","state","currency","phonecode","phone","website","pricelist")))) { return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in updating Distributor, (updating details failed), Please try again later."]); }
			if($Partner->Details->City->State->Country->id != $request->country){
				if($Partner->countries()->detach($Partner->Details->City->State->Country->id) === false) { return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in updating Distributor, (detaching country failed), Please try again later."]); }
				if($Partner->countries()->attach($request->country) === false) { return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in updating Distributor, (attaching country failed), Please try again later."]); }
			}
			if(!$Partner->logins()->update($request->only("email"))) { return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in updating Distributor, (Updating email failed), Please try again later."]); }
			if($Partner->products()->detach() === false) { return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in updating Distributor, (updating products, (Pre-processing) failed), Please try again later."]); }
			foreach($request->product as $k => $v) $Partner->products()->attach([$v	=> ["edition"	=>	$request->edition[$k],"created_by"	=>	$request->user()->partner,"created_at"	=>	date("Y-m-d H:i:s"),"updated_at"	=>	date("Y-m-d H:i:s")]]);
			return redirect()->route("dashboard")->with(["info"=>true,"type"=>"success","text"=>"Distributor, ".$request->name." updated successfully"]);
		
		}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function destroy(Partner $distributor){
		$distributor->update(["status"	=>	"INACTIVE"]);
		return redirect()->route("dashboard")->with(["info"=>true,"type"=>"success","text"=>"Distributor, ".$distributor->name." deleted successfully"]);
	}
	
	static function RulesMessages(){
		return [
			["code"	=>	"required|unique:partners,code",
			 "name"	=>	"required",
			 "email"	=>	"required|email|unique:partner_logins,email",
			 "country"	=>	"required|exists:countries,id",
			 "phone"	=>	"required",
			 "pricelist"	=>	"required|exists:price_lists,code,status,ACTIVE",
			 "state"	=>	"nullable|exists:states,id,country," . Request()->country,
			 "city"	=>	"nullable|exists:cities,id,state," . Request()->state,
			 "product.0"	=>	"required"
			],
			["code.required"	=>	"Distributor code is mandatory.",
			 "code.unique"	=>	"Distributor code entered is already taken.",
			 "name.required"	=>	"Name required.",
			 "email.required"	=>	"Email is a madatory field.",
			 "email.email"	=>	"Email entered doesn't seems to be valid.",
			 "email.unique"	=>	"Email provided is already in records.",
			 "country.required"	=>	"Country Required.",
			 "country.exists"	=>	"Country selected in not there in records.",
			 "phone.required"	=>	"Phone number is required.",
			 "pricelist.required"	=>	"Select Pricelist.",
			 "pricelist.exists"	=>	"Price list selected is not a active Price List.",
			 "state.exists"	=>	"State selected is not listed under the selected country.",
			 "city.exists"	=>	"City selected is not listed under the selected state.",
			 "product.0.required"	=>	"Assign atleast One product."
			]
		];
	}
	
	private function NewDetailsCode(){
		$PD = new \App\Models\PartnerDetails;
		return $PD->NextCode();
	}

	static function Request2DetailArray($request, $fields){
		$DetailsArray = [];
		foreach($fields as $field){
			foreach($request->$field as $k => $v){
				$DetailsArray[$k][$field] = $v;
			}
		}
		return $DetailsArray;
			
		$MyVals[$Field] = $request->$Field[$k];
		foreach($request->product as $k => $v){
			$MyVals = [];
			foreach($fields as $Field) $MyVals[$Field] = $request->$Field[$k];
			$DetailsArray[] = $MyVals;
		}
		return $DetailsArray;
	}

	public function lists($page,$items = 40){
		$Distributors = Partner::select("partners.code AS pcode","partners.name AS name"
													 ,DB::raw("CONCAT('+',partner_details.phonecode,'-',partner_details.phone) AS phone"),DB::raw("GETEMAILS(partners.code) AS email"),DB::raw("GETCOUNTRIES(partners.code) AS country")
													 ,"partner_roles.rolename"
													 ,"partner_relations.parent"
													)
			->leftJoin("partner_details","partners.code","=","partner_details.partner")
			->leftJoin("partner_roles","partner_roles.partner","=","partners.code")
			->leftJoin("partner_relations","partner_relations.partner","=","partners.code")
			->where("partners.status","ACTIVE")->where("partner_roles.rolename","distributor");
		if(!Request()->_company) $Distributors->where("partner_relations.parent",Auth()->guard("api")->user()->partner);
		return $Distributors->get();
		//return DB::select("SELECT partners.code AS code, partners.name AS name, CONCAT('+',partner_details.phonecode,'-',partner_details.phone) AS phone, GETEMAILS(partners.code) AS email, GETCOUNTRIES(partners.code) as country FROM partners LEFT JOIN partner_details ON partners.code = partner_details.partner INNER JOIN partner_roles ON partner_roles.partner = partners.code AND partner_roles.rolename = 'dealer' AND  partners.status = 'ACTIVE' LIMIT ".(($page-1)*$items).",".($page*$items));
	}
	
	public function distributor(Partner $distributor){
		return $distributor->with("details","logins","products","editions","countries","pricelist")->whereStatus("ACTIVE")->whereCode($distributor->code)->get()->map(function($item, $key){
			foreach($item->countries as $k => $CObj) { if($k == 0) { $item->country = $CObj->name; $item->currency = $CObj->currency; $item->phonecode = $CObj->phonecode; } unset($item->countries[$k]); $item->countries[$k] = $CObj->name;  }
			$item->details->city = DB::select("SELECT name FROM cities WHERE id = '".($item->details->city."'"))[0]->name;
			$item->details->state = DB::select("SELECT name FROM states WHERE id = '".($item->details->state."'"))[0]->name;
			foreach($item->editions as $k => $editObj) { $item->editions[$editObj['code']] = $editObj['name']; unset($item->editions[$k]); }
			$item->email = $item->logins->first()->email; unset($item->logins);
			$product_editions = [];
			foreach($item->products as $k => $prodObj) {
				if(!isset($item->products[$prodObj->code])) $item->products[$prodObj->code] = [$prodObj->name, $prodObj->private];
				$product_editions[$prodObj->code][] = $prodObj->pivot->edition;
				unset($item->products[$k]);
			}
			$item->product_editions = $product_editions;
			if(!empty($item->pricelist->isNotEmpty())) $item->price_list = $item->pricelist->first()->name;
			return $item;
		})->first();
	}

	public function dashboard(){
		return view("distributor.dashboard");
	}
	
	public function password(){
		return view("distributor.password");
	}
	
	public function address(){
		$user = Auth()->user();
		$Partner = \App\Models\Distributor::whereCode($user->partner)->with(['Countries'])->first();
		$States = DB::table("states")->select("id","name")->whereCountry($Partner->Details->City->State->country)->get();
		$Cities = DB::table("cities")->select("id","name")->whereState($Partner->Details->City->state)->get();
		return view("distributor.address",compact("Partner","States","Cities"));
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
	
	public function changeaddress(Request $Request){
		$Partner = \App\Models\Distributor::whereCode(Auth()->user()->partner)->with(['Countries'])->first();
		if(Auth()->user()->email != $Request->email) {
			$ES = $Partner->Logins()->whereEmail(Auth()->user()->email)->first();
			$ES->email = $Request->email; $ES->save();
		}
		$Partner->details->fill($Request->except("submit",'name','email','country'));
		if($Partner->name != $Request->name) $Partner->name = $Request->name;
		if($Partner->push()) return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Details updated successfully.."]);
		return redirect()->back()->with(["info"=>true,"type"=>"danger","text"=>"Server error, Please try again later."]);
	}
	
	public	function content(){
		$Partner = Auth()->guard("api")->user()->partner()->first();
		//$Partner = Auth()->user()->partner()->first();
		$Childs = $Partner->AllChildren();
		return Partner::select("code","name","status","created_at")->with(["products"=>function($q){
			$q->select("code","name");
		},"customerProducts"=>function($q){
			$q->select("code","name");
		},"customerProducts.editions"=>function($q){
			$q->select("code","name");
		},"products.editions"=>function($q){
			$q->select("code","name");
		},"children"])->whereIn("code",$Childs)->whereNotIn("status",["INACTIVE"])->orderBy("created_at")->get()->groupBy('code');
	}

	public function fetchproducts($User = NULL){
		$User = $User ?: Auth()->user()->partner()->first();
		$Products = $User->products->groupBy("code");
		$ProductEditions = $this->fetcheditions($Products);
		return $Products = $Products->map(function($item, $key) use ($ProductEditions){
			$Obj = $item->first();
			return [$Obj->code,$Obj->name, $ProductEditions[$key]];
		})->values()->toArray();
	}
	
	private function fetcheditions($Products){
		$ProductEditions = $Products->map(function($item, $key){
			return $item->map(function($item, $key){
				return $item->pivot->edition;
			});
		});
		$Editions = \App\Models\Edition::select("code","name")->whereIn("code",$ProductEditions->collapse()->unique())->whereActive("1")->pluck("name","code")->map(function($item, $key){
			return [$key, $item];
		});
		return $ProductEditions->map(function($item, $key) use ($Editions){
			return $item->map(function($item, $key) use ($Editions) {
				return $Editions[$item];
			});
		});
	}
	
	private function GetDistributorProducts($DC){
		return \App\Models\PartnerProduct::select("product","edition")->whereStatus("ACTIVE")->wherePartner($DC)->get();
	}
	
	private function GetPriceListOfDistributor($Code){
		return Partner::with('pricelist.details')->whereCode($Code)->first()->pricelist[0]->details->mapWithKeys(function($item){
			//return $item->id;
			return [implode(":",[$item->product,$item->edition]) => [$item->mop,$item->price,$item->mrp,$item->currency]];
		});
	}
	
	public function products($Code){
		list($Products,$Editions) = $this->GetProductsEditions();
		$Current = $this->GetDistributorProducts($Code);
		$PL = $this->GetPriceListOfDistributor($Code);
		return view("distributor.products",compact("Products","Editions","Current","Code","PL"));
	}
	
	public function updateproducts(Partner $distributor,Request $request){
		list($Products,$Editions) = [$request->product,$request->edition];
		$sync = []; $attach = [];
		$defalut = ["created_by"	=>	$request->user()->partner, "created_at"	=>	date("Y-m-d H:i:s"), "updated_at"	=>	date("Y-m-d H:i:s")];
		foreach($Products as $K => $PC){
			$EC = $Editions[$K];
			if(array_key_exists($PC,$sync)) $attach[] = [$PC => array_merge($defalut,["edition"	=>	$EC])];
			else $sync[$PC] = array_merge($defalut,["edition"	=>	$EC]);
		}
		$distributor->products()->detach();
		if(!empty($sync)){
			$distributor->products()->attach($sync);
			if(!empty($attach)) foreach($attach as $atObj) $distributor->products()->attach($atObj);
		}
		Mail::init()->queue(new DistributorProductModification($distributor))->send($distributor);
		return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Products updated successfully."]);
	}
	
	public function countries(Partner $distributor){
		$Code = $distributor->code;
		$My = $distributor->countries()->pluck("name","countries.id");
		$All = \App\Models\Country::pluck("name","id");
		//compact("Code","My","All");
		return view("distributor.countries",compact("Code","My","All"));
	}
	
	public function updatecountries(Partner $distributor, Request $request){
		$distributor->countries()->sync($request->countries);
		Mail::init()->queue(new DistributorCountryModification($distributor))->send($distributor);
		return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Countries updated successfully."]);
	}
	
	public function transactions($Distributor = NULL){
		return \App\Models\Transaction::select("updated_at","description","price","type")
			->whereDistributor($Distributor?:Auth()->guard("api")->user()->partner)->whereStatus("ACTIVE")->oldest("updated_at")->get()->map(function($item,$key){
				return array_values($item->toArray());
			});
	}
	
	public function customers($Distributor = NULL){
		$customers = $this->get_all_customer_codes_of_distributor($Distributor?:Auth()->guard("api")->user()->partner);
		$Registrations = \App\Models\CustomerRegistration::whereIn('customer',$customers)->with('Customer.ParentDetails','Product','Edition')->get();
		return view('distributor.customers',compact('Registrations'));
	}
	
	public function tickets($Distributor = NULL){
		$Dist = $Distributor?:((Auth()->user())?(Auth()->user()->partner):(Auth()->guard("api")->user()->partner));
		$Tickets = \App\Models\Ticket::where('created_by',$Dist)->with(['Product','Edition'])->get();
		return view('distributor.tickets',compact('Tickets'));
	}
	
	public function resetlogin($Distributor){
		$Partner = Partner::whereCode($Distributor)->with('Logins','Parent.ParentDetails.Roles')->first();
		$Logins = $Partner->Logins[0];
		$pArr = ['id','partner','email','expiry'];
		$vArr = [$Logins->id,$Distributor,$Logins->email,strtotime("+18 Hours")];
		$Code = (new \App\Http\Controllers\KeyCodeController())->KeyEncode($pArr,$vArr);
		Mail::init()->queue(new DistributorLoginReset($Partner,$Code))->to($Partner)->send();
		return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Login reset instructions have been mailed to, ".$Partner->Logins[0]->email]);
		//return [$Partner->code, $Partner->name, $Partner->Logins[0]->email];
	}
	
	public function supportteam($Distributor, Request $request){
		$Dist = \App\Models\Distributor::find($Distributor)->load('Supportteam');
		$Dist->Supportteam[0]->update(['status' => 'INACTIVE']);
		$Dist->Supportteam()->create(['distributor' => $Dist->code, 'supportteam' => $request->supportteam, 'assigned_by' => $Dist->_GETAUTHUSER()->partner]);
		return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Support Team updated successfully"]);
		//return [$Partner->code, $Partner->name, $Partner->Logins[0]->email];
	}
	
	public function support_categories($Distributor, Request $request){
		if(!$Distributor) return redirect()->back()->with(["info"=>true,"type"=>"danger","text"=>"Distributor cannot be empty."]);
		(new \App\Models\DistributorExcludeCategory)->create_new($Distributor,$request->category);
		return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Categories excluded successfully."]);
	}
	
	public function contact_options($Distributor, Request $request){
		(new \App\Models\DistributorContactMethod)->add_new($Distributor,$request->email,$request->sms);
		$DCCM = (new \App\Models\DistributorCustomerContactMethod)->add_new_common($Distributor,$request->customer_email,$request->customer_sms);
		$DCCM->delete_all_exceptions($Distributor);
		if($request->ex_customers && !empty($request->ex_customers)){
			$newcustomer = [];
			foreach($request->ex_customers as $customer){
				$newcustomer[$customer] = [null,null];
				if($request->exEmail[$customer]) $newcustomer[$customer][0] = $request->exEmail[$customer];
				if($request->exSms[$customer]) $newcustomer[$customer][1] = $request->exSms[$customer];
			}
			$DCCM->add_exceptions($Distributor, $newcustomer);
		}
	  return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Contact method updated successfully."]);
	}

	static function get_dealer_codes_of_distributor($distributor){
		return \App\Models\Dealer::with('Parent1')->whereHas('Parent1',function($Q)use($distributor){ $Q->whereParent($distributor); })->pluck('code');
	}
	
	static function get_customer_codes_of_distributor($distributor){
		return \App\Models\Customer::with('Parent1')->whereHas('Parent1',function($Q)use($distributor){ $Q->whereParent($distributor); })->pluck('code');
	}

	static function get_dealer_codes_of_distributors($distributors){
		return \App\Models\Dealer::with('Parent1')->whereHas('Parent1',function($Q)use($distributor){ $Q->whereIn('parent',$distributors); })->pluck('code');
	}
	
	static function get_customer_codes_of_distributors($distributors){
		return \App\Models\Customer::with('Parent1')->whereHas('Parent1',function($Q)use($distributor){ $Q->whereIn('parent',$distributors); })->pluck('code');
	}
	
	public function get_all_customer_codes_of_distributor($distributor){
		$Dealers = $this->get_dealer_codes_of_distributor($distributor)->toArray();
		$Customer = $this->get_customer_codes_of_distributor($distributor)->toArray();
		return array_merge($Customer,\App\Http\Controllers\DealerController::get_customer_codes_of_dealers($Dealers)->toArray());
	}
	
	private function getAuthUser(){
		return (Auth()->user())?:(Auth()->guard("api")->user());
	}

	private function AssignDefaultSupportTeam($distributor){
		$supportteam = \App\Models\DefaultSupportTeam::first()->supportteam;
		$assigned_by = $this->getAuthUser()->partner;
		return \App\Models\DistributorSupportTeam::create(compact('distributor','supportteam','assigned_by'));
	}

	private function AssignContactMethods($distributor){
		(new \App\Models\DistributorContactMethod)->add_new($distributor,'Yes',null);
	}
	
	public function detail_search(Request $request){
		$ORM = \App\Models\Distributor::with('Countries','Products','Editions');
		if($request->distributor) $ORM = $this->ModifyORMForSearch($ORM,$request->distributor);
		if($request->country) $ORM = $this->ModifyORMForFilter($ORM,'Country',$request->country);
		if($request->product) $ORM = $this->ModifyORMForFilter($ORM,'Product',$request->product);
		if($request->edition) $ORM = $this->ModifyORMForFilter($ORM,'Edition',$request->edition);
		return $ORM->get();
	}
	
	private function ModifyORMForSearch($ORM,$Term){
		$like = '%'.$Term.'%';
		return $ORM->where(function($Q) use($like){
			$Q->orWhere('code','like',$like)
				->orWhere('name','like',$like)
				->orWhereHas('Details',function($Q) use($like){ $Q->where('phone','like',$like); })
				->orWhereHas('Logins',function($Q) use($like){ $Q->where('email','like',$like); })
				;
		});
	}
	
	private function ModifyORMForFilter($ORM,$Item,$Term){
		switch($Item){
			case 'Country':
				$ORM = $ORM->where(function($Q) use($Term){
					$Q->whereHas('Countries',function($Q) use($Term){ $Q->where(\DB::raw('`countries`.`id`'),$Term); });
				});
				break;
			case 'Product':
				$ORM = $ORM->where(function($Q) use($Term){
					$Q->whereHas('Products',function($Q) use($Term){ $Q->where('code',$Term); });
				});
				break;
			case 'Edition':
				$ORM = $ORM->where(function($Q) use($Term){
					$Q->whereHas('Editions',function($Q) use($Term){ $Q->where('code',$Term); });
				});
				break;
		}
		return $ORM;
	}

}