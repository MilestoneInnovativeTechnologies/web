<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partner;
use Validator;
use DB;
use Hash;
use App\Libraries\Mail;
use App\Mail\NewDealer;
use App\Mail\DealerProductModification;
use App\Mail\DealerCountryModification;

class DealerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
			return view("dealer.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
			$Dealer = new Partner();
			$Code = $Dealer->DealerNextCode();
			$Editions = [];
			$Products = \App\Models\PartnerProduct::with(["products"	=>	function($q){
				$q->select("code","name","private");
			},"editions" => function($q){
					$q->select("code","name","private");
			}])->wherePartner(Request()->user()->partner)->get()->groupBy("product")->map(function($item, $key) use(&$Editions){
				$RetArray = ["name"=>$item[0]->products->name,"private"=>$item[0]->products->private,"editions"=>[],"code"=>$item[0]->products->code];
				foreach($item as $Obj){ $RetArray["editions"][$Obj->editions->code] = $Obj->editions->private; $Editions[$Obj->editions->code] = $Obj->editions->name; }
				return $RetArray;
			})->filter();
			//return compact("Products","Editions","Code");
      return view("dealer.create",compact("Products","Editions","Code"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
			//return $request->all();
			$RM = $this->RulesMessages(); $Rules = $RM[0]; $Messages = $RM[1];
			$Validate = Validator::make($request->all(),$Rules,$Messages);
			if($Validate->fails()) return redirect()->back()->withInput()->withErrors($Validate);
			$request->merge(["created_by"=>$request->user()->partner]);
      $Partner = Partner::create($request->all());
			if(!$Partner) return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in creating Dealer, Please try again later."]);
			if($Partner->countries()->attach($request->country) === false) { $Partner->delete(); return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in creating Dealer, (Adding country failed), Please try again later."]); }
			if(!$Partner->details()->create(array_merge(["code"=>$this->NewDetailsCode()],$request->only("address1","address2","city","state","currency","phonecode","phone","website")))) { $Partner->delete(); return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in creating Dealer, (Adding details failed), Please try again later."]); }
			if(!($login = $Partner->logins()->create(array_merge(["password"=>bcrypt($pwd = str_random(8)),"created_by"=>$request->user()->partner],$request->only("email")))->id)) { $Partner->delete(); return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in creating Dealer, (Creating login failed), Please try again later."]); }
			if($Partner->products()->detach() === false) { $Partner->delete(); return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in creating Dealer, (Adding products, (Pre-processing) failed), Please try again later."]); }
			foreach($request->product as $k => $v) $Partner->products()->attach([$v	=> ["edition"	=>	$request->edition[$k],"created_by"	=>	$request->created_by,"created_at"	=>	date("Y-m-d H:i:s"),"updated_at"	=>	date("Y-m-d H:i:s")]]);
			if(!$Partner->parent()->create(["parent"=>$request->user()->partner])) { $Partner->delete(); return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in creating Dealer, (Adding relation failed), Please try again later."]); }
			$RoleCode = \App\Models\Role::whereName("dealer")->first()->code;
			if($Partner->roles()->attach($RoleCode,["login"=>$login,"created_by"=>$request->user()->partner,"created_at"=>date("Y-m-d H:i:s"),"updated_at"=>date("Y-m-d H:i:s")]) === false) { $Partner->delete(); return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in creating Dealer, (Adding roles failed), Please try again later."]); }
			Mail::init()->queue(new NewDealer($request->all(),$request->user()->partner,$login,$Partner->code))->send($Partner);
			return redirect()->route("dashboard")->with(["info"=>true,"type"=>"success","text"=>"Dealer, ".$request->name." added successfully"]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($Code)
    {
        return view("dealer.show",compact("Code"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($Code)
    {
			$Array = ["code","name","country","phonecode","currency","phone","email","address1","address2","city","state","website"];
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
			$Editions = [];
			$Products = \App\Models\PartnerProduct::with(["products"	=>	function($q){
				$q->select("code","name","private");
			},"editions" => function($q){
					$q->select("code","name","private");
			}])->wherePartner(Request()->user()->partner)->get()->groupBy("product")->map(function($item, $key) use(&$Editions){
				$RetArray = ["name"=>$item[0]->products->name,"private"=>$item[0]->products->private,"editions"=>[],"code"=>$item[0]->products->code];
				foreach($item as $Obj){ $RetArray["editions"][$Obj->editions->code] = $Obj->editions->private; $Editions[$Obj->editions->code] = $Obj->editions->name; }
				return $RetArray;
			})->filter();
			/*$Products = \App\Models\Product::select("code","name","private")->with(["editions" => function($q){
				$q->select("code","name","private");
			}])->whereActive(1)->get()->groupBy("code")->map(function($item, $key){
				return ($item[0]->editions->isEmpty()) ? NULL : ["name"=>$item[0]->name,"private"=>$item[0]->private,"editions"=>$item[0]->editions->pluck("private","code"),"code"=>$item[0]->code];
			})->filter();
			$Editions = \App\Models\Edition::select("code","name")->whereActive(1)->get()->pluck("name","code");*/
			$Update = true;
			//return compact("Products","Editions","Code","Update","Details");
			return view("dealer.create",compact("Products","Editions","Code","Update","Details"));
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
			if($Partner->Logins[0]->email == $request->email) unset($Rules["email"]);
			$Validate = Validator::make($request->all(),$Rules,$Messages);
			if($Validate->fails()) return redirect()->back()->withInput()->withErrors($Validate);
			if(!$Partner->update($request->all())) return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in updating Dealer, Please try again."]);
			if(!$Partner->details()->update(array_merge(["code"=>$request->code],$request->only("address1","address2","city","state","currency","phonecode","phone","website")))) { return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in updating Dealer, (updating details failed), Please try again later."]); }
			if($Partner->Details->City->State->Country->id != $request->country){
				if($Partner->countries()->detach($Partner->Details->City->State->Country->id) === false) { return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in updating Dealer, (detaching country failed), Please try again later."]); }
				if($Partner->countries()->attach($request->country) === false) { return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in updating Dealer, (attaching country failed), Please try again later."]); }
			}
			if($Partner->Logins[0]->email != $request->email){
				if(session("_rolename") == "dealer"){
					if(!$Partner->currentlogin()->update($request->only("email"))) { return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in updating Dealer, (Updating email failed), Please try again later."]); }
				} else {
					if(!$Partner->Logins[0]->update($request->only("email"))) { return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in updating Dealer, (Updating email failed), Please try again later."]); }
				}
			}
			if($Partner->products()->detach() === false) { return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in updating Dealer, (updating products, (Pre-processing) failed), Please try again later."]); }
			foreach($request->product as $k => $v) $Partner->products()->attach([$v	=> ["edition"	=>	$request->edition[$k],"created_by"	=>	$request->user()->partner,"created_at"	=>	date("Y-m-d H:i:s"),"updated_at"	=>	date("Y-m-d H:i:s")]]);
			return redirect()->route("dashboard")->with(["info"=>true,"type"=>"success","text"=>"Dealer, ".$request->name." updated successfully"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Partner $dealer)
    {
			$dealer->update(["status"	=>	"INACTIVE"]);
			return redirect()->route("dashboard")->with(["info"=>true,"type"=>"success","text"=>"Dealer, ".$dealer->name." deleted successfully"]);
    }
	
	public function dashboard(){
		return view("dealer.dashboard");
	}
	
	public function password(){
		return view("dealer.password");
	}
	
	public function address(){
		$user = Auth()->user();
		$Partner = \App\Models\Partner::find($user->partner);
		$Details = $Partner->details;
		$Countries = $Partner->countries()->get()->pluck("id");
		$States = DB::table("states")->select("id","name")->whereIn("country",$Countries)->get();
		$City = DB::table("cities")->select("id","name")->whereId($Details->city)->get()->first();
		return view("dealer.address",compact("Details","States","City"));
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
		$user = Auth()->user();
		$Partner = \App\Models\Partner::find($user->partner);
		$Partner->details->fill($Request->except("submit"));
		if($Partner->push()) return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Details updated successfully.."]);
		return redirect()->back()->with(["info"=>true,"type"=>"danger","text"=>"Server error, Please try again later."]);
	}
	
	public function mycustomers($User = NULL){
		$User = ($User)?:Auth()->guard("api")->user()->partner()->first();
		$Childs = $this->children($User);
		return $CustRegs = DB::table("customer_registrations")
			->join("partners","partners.code","=","customer_registrations.customer")
			->select("name","customer","product","edition","registered_on","customer_registrations.created_by","customer_registrations.created_at")
			->whereIn("customer",$Childs)
			->orderBy("created_at","desc")
			->get()->map(function($item, $key){
				$item->created_since = ($item->created_at) ? ($this->DateDiffDays("now",$item->created_at)) : (NULL);
				$item->registered_since = ($item->registered_on) ? ($this->DateDiffDays("now",$item->registered_on)) : (NULL);
				return $item;
			});
	}
	
	public function myproducts($User = NULL){
		$User = ($User)?:Auth()->guard("api")->user()->partner()->first();
		$Products = $User->products->groupBy("code");
		$ProductEditions = $this->editions($Products);
		return $Products = $Products->map(function($item, $key) use ($ProductEditions){
			$Obj = $item->first();
			return [$Obj->code,$Obj->name, $ProductEditions[$key]];
		})->values()->toArray();
	}
	
	public function mydetails($user = NULL){
		$user = ($user)?:Auth()->guard("api")->user();
		$parent = $user->parent->parent;
		$User = array_replace($this->PartnerDetails($user->partner),["email"	=> $user->email]);
		$Parent = $this->PartnerDetails($parent,true);
		return compact("User","Parent");
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
	
	private function children($User){
		return $User->children->pluck("partner")->toArray();
	}
	
	private function editions($Products){
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
	
	private function DateDiffDays($Fut, $Pst){
		$up = new \DateTime($Fut); $dw = new \DateTime($Pst);
		$int = $up->diff($dw); return $int->format("%a");
	}


	public function lists($page,$items = 40){
		$Dealers = Partner::select("partners.code AS pcode","partners.name AS name"
													 ,DB::raw("CONCAT('+',partner_details.phonecode,'-',partner_details.phone) AS phone"),DB::raw("GETEMAILS(partners.code) AS email"),DB::raw("GETCOUNTRIES(partners.code) AS country")
													 ,"partner_roles.rolename"
													 ,"partner_relations.parent"
													)
			->leftJoin("partner_details","partners.code","=","partner_details.partner")
			->leftJoin("partner_roles","partner_roles.partner","=","partners.code")
			->leftJoin("partner_relations","partner_relations.partner","=","partners.code")
			->where("partners.status","ACTIVE")->where("partner_roles.rolename","dealer");
		if(!Request()->_company) $Dealers->where("partner_relations.parent",Auth()->guard("api")->user()->partner);
		return $Dealers->get();
		//return DB::select("SELECT partners.code AS code, partners.name AS name, CONCAT('+',partner_details.phonecode,'-',partner_details.phone) AS phone, GETEMAILS(partners.code) AS email, GETCOUNTRIES(partners.code) as country FROM partners LEFT JOIN partner_details ON partners.code = partner_details.partner INNER JOIN partner_roles ON partner_roles.partner = partners.code AND partner_roles.rolename = 'dealer' AND  partners.status = 'ACTIVE' LIMIT ".(($page-1)*$items).",".($page*$items));
	}
	
	public function dealer(Partner $dealer){
		return $dealer->with("details","logins","products","editions","countries","pricelist")->whereStatus("ACTIVE")->whereCode($dealer->code)->get()->map(function($item, $key){
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
	
	static function RulesMessages(){
		return [
			["code"	=>	"required|unique:partners,code",
			 "name"	=>	"required",
			 "email"	=>	"required|email|unique:partner_logins,email",
			 "country"	=>	"required|exists:countries,id",
			 "phone"	=>	"required",
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
	
	public function report(Partner $dealer){
		if(Auth()->guard("api")->user()->partner != $dealer->parent->parent || $dealer->status != "ACTIVE") return "0";
		$Products = $this->myproducts($dealer);
		$Customers = $this->mycustomers($dealer);
		$Details = $this->PartnerDetails($dealer->code, true);
		return compact("Products","Customers","Details");
	}
	
	public function products($Code){
		list($Products,$Editions) = $this->GetProductsEditions();
		$Current = $this->GetDealerProducts($Code);
		$PL = $this->GetPriceListOfDistributor(Request()->user()->partner);
		//return compact("Products","Editions","Current","Code","PL");
		return view("dealer.products",compact("Products","Editions","Current","Code","PL"));
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
	
	private function GetDealerProducts($DC){
		return \App\Models\PartnerProduct::select("product","edition")->whereStatus("ACTIVE")->wherePartner($DC)->get();
	}
	
	private function GetPriceListOfDistributor($Code){
		return Partner::with('pricelist.details')->whereCode($Code)->first()->pricelist[0]->details->mapWithKeys(function($item){
			//return $item->id;
			return [implode(":",[$item->product,$item->edition]) => [$item->mop,$item->price,$item->mrp,$item->currency]];
		});
	}
	
	public function updateproducts(Partner $dealer,Request $request){
		list($Products,$Editions) = [$request->product,$request->edition];
		$sync = []; $attach = [];
		$defalut = ["created_by"	=>	$request->user()->partner, "created_at"	=>	date("Y-m-d H:i:s"), "updated_at"	=>	date("Y-m-d H:i:s")];
		foreach($Products as $K => $PC){
			$EC = $Editions[$K];
			if(array_key_exists($PC,$sync)) $attach[] = [$PC => array_merge($defalut,["edition"	=>	$EC])];
			else $sync[$PC] = array_merge($defalut,["edition"	=>	$EC]);
		}
		$dealer->products()->detach();
		if(!empty($sync)){
			$dealer->products()->attach($sync);
			if(!empty($attach)) foreach($attach as $atObj) $dealer->products()->attach($atObj);
		}
		Mail::init()->queue(new DealerProductModification($dealer))->send($dealer->load('Logins'));
		return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Products updated successfully."]);
	}
	
	public function countries(Partner $dealer){
		$Code = $dealer->code;
		$Data = $dealer->with("countries","parentDetails.countries")->whereCode($Code)->first();
		$My = $Data->countries->mapWithKeys(function($obj){
			return [$obj->id => $obj->name];
		});
		$All = $Data->parentDetails[0]->countries->mapWithKeys(function($obj){
			return [$obj->id => $obj->name];
		});
		compact("Code","My","All");
		return view("dealer.countries",compact("Code","My","All"));
	}
	
	public function updatecountries(Partner $dealer, Request $request){
		$dealer->countries()->sync($request->countries);
		Mail::init()->queue(new DealerCountryModification($dealer))->send($dealer);
		return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Countries updated successfully."]);
	}
	
	static function get_customer_codes_of_dealer($dealer){
		return \App\Models\Customer::with('Parent1')->whereHas('Parent1',function($Q)use($dealer){ $Q->whereParent($dealer); })->pluck('code');
	}
	
	static function get_customer_codes_of_dealers($dealers){
		return \App\Models\Customer::with('Parent1')->whereHas('Parent1',function($Q)use($dealers){ $Q->whereIn('parent', $dealers); })->pluck('code');
	}
	
	public function tickets($Dealer = NULL){
		$Dlr = $Dealer?:((Auth()->user())?(Auth()->user()->partner):(Auth()->guard("api")->user()->partner));
		$Tickets = \App\Models\Ticket::where('created_by',$Dlr)->with(['Product','Edition'])->get();
		return view('dealer.tickets',compact('Tickets'));
	}
	
	public function detail_search(Request $request){
		$ORM = \App\Models\Dealer::with('Countries','Products','Editions');
		if($request->dealer) $ORM = $this->ModifyORMForSearch($ORM,$request->dealer);
		if($request->country) $ORM = $this->ModifyORMForFilter($ORM,'Country',$request->country);
		if($request->product) $ORM = $this->ModifyORMForFilter($ORM,'Product',$request->product);
		if($request->edition) $ORM = $this->ModifyORMForFilter($ORM,'Edition',$request->edition);
		if($request->distributor) $ORM = $this->ModifyORMForFilter($ORM,'Distributor',$request->distributor);
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
			case 'Distributor':
				$ORM = $ORM->where(function($Q) use($Term){
					$Q->whereHas('Distributor',function($Q) use($Term){ $Q->where('code',$Term); });
				});
				break;
		}
		return $ORM;
	}
	
}
