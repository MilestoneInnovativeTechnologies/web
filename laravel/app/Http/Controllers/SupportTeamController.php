<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupportTeam;
use App\Models\Partner;

use Validator;

class SupportTeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('tst.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
				$Data["code"] = (new SupportTeam())->NewCode();
        return view('tst.form',compact("Data"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
			//return redirect()->back()->withInput();
			$Data = new SupportTeam();
			$Validator = Validator::make($request->all(),$Data->ValidationRules(),$Data->ValidationMessages());
			if($Validator->fails()) return redirect()->back()->withErrors($Validator)->withInput();
			$Partner = $this->AddBasicDetails($request);
			$this->AddLogins($Partner,$request);
			$this->AddRole($Partner,$request);
			$this->AddDetails($Partner,$request);
			$this->AddCountry($Partner,$request);
			$this->AddRelation($Partner,$request);
			$this->AddPrivilage($Partner,$request);
			$this->AddDefault($Partner,$request);
			return redirect()->route('tst.index')->with(['info'	=>	true, 'type'	=>	'success',	'text'	=>	'Technical Support Team Created Successfully..']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($code)
    {
        $Data = SupportTeam::find($code);
				return view('tst.show',compact("Data"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($code)
    {
        $Data = SupportTeam::find($code)->toArray();
				$Data = $this->flattern($Data);
				$Update = true;
				return view("tst.form",compact("Data","Update"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($code, Request $request)
    {
			$DBData = $this->flattern(SupportTeam::find($code)->toArray());
			$FieldToFun = $this->FieldToFunctionArray();
			$ExecFields = ["Country"=>[],"Detail"=>[],"Login"=>[],"Basic"=>[],'Privilage'=>[],'Default'=>[]];
			$Rules = array_map(function($Rule){ return str_replace('required','nullable',$Rule); },SupportTeam::ValidationRules()); $Messages = SupportTeam::ValidationMessages();
			foreach($DBData as $K => $V){
				if($V != $request->$K) $ExecFields[(array_key_exists($K,$FieldToFun))?$FieldToFun[$K]:"Detail"][] = $K;
			}
			foreach($ExecFields as $Fun => $FieldAry){
				$Status = $this->{"Update".$Fun}($code,$FieldAry,$request,$Rules,$Messages);
				if($Status !== true) return $Status;
			}
			return redirect()->route('tst.edit',['code'	=>	$code])->with(['info'	=>	true, 'type'	=>	'success',	'text'	=>	'Details Updated Successfully..']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function delete($code)
    {
        $ST = SupportTeam::find($code);
				$ST->update(["status"	=> "INACTIVE"]);
				return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Status of " . $ST->name . ", changed to INACTIVE."]);
    }
    public function undelete($code)
    {
        $ST = SupportTeam::find($code);
				$ST->update(["status"	=> "ACTIVE"]);
				return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Status of " . $ST->name . ", changed to ACTIVE."]);
    }

		private function AddBasicDetails($request){
			$Fields = ['code','name'];
			$Extra = ['created_by'	=>	$request->user()->partner, 'status'	=>	'ACTIVE'];
			$DBFields = array_merge($request->only($Fields),$Extra);
      return $Partner = Partner::create($DBFields);
		}
	
		private function AddLogins($Partner, $request){
			$Fields = ['email'];
			$Extra = ['password'	=>	bcrypt(str_random(8)), 'created_by'	=>	$request->user()->partner];
			$DBFields = array_merge($request->only($Fields),$Extra);
			$Partner->Logins()->create($DBFields);
		}
	
		private function AddRole($Partner,$request){
			$Role = $this->GetRoleCode();
			$Extra = ['created_by'	=>	$request->user()->partner, 'login'	=> Partner::find($Partner->code)->Logins[0]->id, 'created_at'	=>	date("Y-m-d H:i:s"), 'updated_at'	=>	date("Y-m-d H:i:s")];
			$Partner->Roles()->attach($Role,$Extra);
		}
	
		private function GetRoleCode(){
			$RoleName = 'supportteam';
			return \App\Models\Role::whereName($RoleName)->first()->code;
		}
	
		private function AddDetails($Partner,$request){
			$Fields = ['address1','address2','city','state','currency','phonecode','phone','website'];
			$Extra = ['status'	=>	'ACTIVE', 'code'	=>	NULL];
			$DBFields = array_merge($request->only($Fields),$Extra);
      $Partner->Details()->create($DBFields);
		}
	
		private function AddCountry($Partner,$request){
      $Partner->Countries()->attach($request->country);
		}

		private function AddRelation($Partner,$request){
      return $Partner->Parent()->create(['parent'	=>	$request->user()->partner]);
		}

		private function AddPrivilage($Partner,$request){
			$Privilage = ($request->privilaged == 'YES') ? 'YES' : 'NO';
      return $Partner->Privilage()->create(['privilage'	=>	$Privilage]);
		}

		private function AddDefault($Partner,$request){
			if($request->default == 'YES') {
				\App\Models\DefaultSupportTeam::truncate();
				\App\Models\DefaultSupportTeam::create(['supportteam'	=>	$Partner->code]);
			}
			return $Partner;
		}
	
		private function flattern($Data){
			foreach(['address1','address2','website','phonecode','phone','currency','city','state'] as $A)
				$Data[$A] = $Data['details'][$A];
			$Data['email'] = $Data['logins'][0]['email'];
			$Data['country'] = $Data['details']['city']['state']['country']['id'];
			$Data['city'] = $Data['details']['city']['id'];
			$Data['privilaged'] = $Data['privilage']['privilage'];
			$Data['default'] = $Data['defaultst']['supportteam'];
			unset($Data['details'],$Data['logins'],$Data['roles'],$Data['privilage'],$Data['defaultst']);
			return $Data;
		}
	
		private function FieldToFunctionArray(){
			return [
				'name'	=>	'Basic',
				'code'	=>	'Basic',
				'country'	=>	'Country',
				'email'	=>	'Login',
				'privilaged'	=>	'Privilage',
				'default'	=>	'Default',
			];
		}
	
		private function UpdateBasic($Code, $Fields, $request, $Rules, $Msgs){
			if(empty($Fields)) return true;
			$Validator = Validator::make($request->only($Fields),$Rules,$Msgs);
			if($Validator->fails()) return redirect()->back()->withErrors($Validator)->withInput()->with(['info'	=>	true, 'type'	=>	'danger',	'text'	=>	'Error occured in updating basic details']);
			Partner::find($Code)->update($request->only($Fields));
			return true;
		}
	
		private function UpdateDetail($Code, $Fields, $request, $Rules, $Msgs){
			if(empty($Fields)) return true;
			$Validator = Validator::make($request->only($Fields),$Rules,$Msgs);
			if($Validator->fails()) return redirect()->back()->withErrors($Validator)->withInput()->with(['info'	=>	true, 'type'	=>	'danger',	'text'	=>	'Error occured in updating details']);
			Partner::find($Code)->Details()->update($request->only($Fields));
			return true;
			
		}
	
		private function UpdateCountry($Code, $Fields, $request, $Rules, $Msgs){
			if(empty($Fields)) return true;
			$Validator = Validator::make($request->only($Fields),$Rules,$Msgs);
			if($Validator->fails()) return redirect()->back()->withErrors($Validator)->withInput()->with(['info'	=>	true, 'type'	=>	'danger',	'text'	=>	'Error occured in updating country details']);
			$AdrCountry = Partner::whereCode($Code)->with("Details.City.State.Country")->first()->Details->City->State->Country->id;
			$UpdCountry = $request->country;
			$P = Partner::find($Code);
			$P->Countries()->detach($AdrCountry);
			$P->Countries()->attach($UpdCountry);
			return true;
		}
	
		private function UpdateLogin($Code, $Fields, $request, $Rules, $Msgs){
			if(empty($Fields)) return true;
			$Validator = Validator::make($request->only($Fields),$Rules,$Msgs);
			if($Validator->fails()) return redirect()->back()->withErrors($Validator)->withInput()->with(['info'	=>	true, 'type'	=>	'danger',	'text'	=>	'Error occured in updating email']);
			Partner::find($Code)->Logins()->first()->update($request->only($Fields));
			return true;
		}
	
		private function UpdatePrivilage($Code, $Fields, $request, $Rules, $Msgs){
			if(empty($Fields)) return true;
			//$Validator = Validator::make($request->only($Fields),$Rules,$Msgs);
			//if($Validator->fails()) return redirect()->back()->withErrors($Validator)->withInput()->with(['info'	=>	true, 'type'	=>	'danger',	'text'	=>	'Error occured in updating email']);
			$privilage = ($request->privilaged == 'YES') ? 'YES' : 'NO';
			Partner::find($Code)->Privilage()->update(['privilage'=>$privilage]);
			return true;
		}
	
		private function UpdateDefault($Code, $Fields, $request, $Rules, $Msgs){
			if(empty($Fields)) return true;
			//$Validator = Validator::make($request->only($Fields),$Rules,$Msgs);
			//if($Validator->fails()) return redirect()->back()->withErrors($Validator)->withInput()->with(['info'	=>	true, 'type'	=>	'danger',	'text'	=>	'Error occured in updating email']);
			if($request->default == 'YES'){
				$this->AddDefault(Partner::find($Code),$request);
			}
			return true;
		}
	
		public function distributors($Code){
			$Data = SupportTeam::find($Code)->load(['Distributors.Partner'	=>	function($Q){
				$Q->with('Details','Logins');
			}]);
			return view('tst.distributors',compact('Code','Data'));
		}
	
		public function distributors_assign($Code){
			$DST = new \App\Models\DistributorSupportTeam();
			$SelfAssigned =  $DST->whereSupportteam($Code)->get();
			$OthersAssigned = $DST->where('supportteam','<>',$Code)->get();
			$AnyAssigedCode = $DST->pluck('distributor')->toArray();
			$Unassigned = \App\Models\Distributor::whereNotIn('code',$AnyAssigedCode)->get();
			return view('tst.distributors_assign',compact('Code','SelfAssigned','OthersAssigned','Unassigned'));
		}
	
		public function customers($Code){
			$ORM = \App\Models\CustomerSupportTeam::whereSupportteam($Code)->withDistributor()->withDealer();
			if(Request()->search_text != "") $ORM->whereHas('Partner',function($Q){ $Q->where('name','like','%'.Request()->search_text.'%'); });
			$Data = $ORM->paginate(15);
			$Pagination = $Data->links();
			return view('tst.customers',compact('Code','Data','Pagination'));
			//return SupportTeam::whereCode($Code)->with('Customers')->get();
		}
	
		public function customers_assign($Code){
			$Distributors = \App\Models\Distributor::pluck('name','code');
			return view('tst.customers_assign',compact('Code','Distributors'));
		}
	
		public function update_distributors($Code, Request $request){
			$Old = $request->old_dist; $New = $request->distributor;
			$Add = array_values(array_diff((array) $New,(array) $Old)); $Del = array_values(array_diff((array) $Old,(array) $New));
			if(!empty($Del)) foreach($Del as $D) $this->DelDistributorSupportTeam($D);
			if(!empty($Add)) foreach($Add as $D) $this->AddDistributorSupportTeam($D,$Code);
			return 1;
		}

		private function getAuthUser(){
			return (Auth()->user())?:Auth()->guard('api')->user();
		}
	
		private function AddDistributorSupportTeam($D,$S){
			$Cond = ['distributor'=>$D]; $Data = ['assigned_by'	=>	$this->getAuthUser()->partner,'supportteam'=>$S];
			$DST = new \App\Models\DistributorSupportTeam();
			if($DST->where($Cond)->get()->isNotEmpty()) $this->DelDistributorSupportTeam($D);
			return \App\Models\DistributorSupportTeam::create(array_merge($Cond,$Data));
		}
	
		private function DelDistributorSupportTeam($D){
			$Cond = ['distributor'=>$D]; $Set = ['status'	=>	'INACTIVE'];
			return \App\Models\DistributorSupportTeam::where($Cond)->update($Set);
		}
	
		public function get_dist_customers(Request $request){
			if(!$request->D) return [];
			$CCodes = $this->getAllCustomerCodeOfDistributor($request->D);
			$Customers = $this->getCustomerDetailsOfCodes($CCodes);
			$CST = \App\Models\CustomerSupportTeam::select('customer','supportteam','product','edition')->whereIn('customer',$CCodes)->get()->groupBy('customer');
			return ['customers'=>$Customers, 'assigned'=>$CST];
		}
	
		private function getAllCustomerCodeOfDistributor($Code){
			return \App\Models\PartnerRelation::where(['parent'=>$Code])->with(['Children','Partnerroles'])->get()->map(function($Obj){
				if($Obj->Children->isEmpty() && $Obj->Partnerroles->contains('rolename','customer')) return $Obj->partner;
				if($Obj->Children->isEmpty() && $Obj->Partnerroles->contains('rolename','dealer')) return null;
				return $Obj->Children->pluck('partner')->toArray();
			})->filter()->flatten()->toArray();
		}
	
		private function getCustomerDetailsOfCodes($Codes){
			return \App\Models\CustomerRegistration::select('customer','seqno','product','edition')->with([
				'Customer'	=>	function($Q){ $Q->select('code','name')->with([
					'Details'	=>	function($Q){ $Q->select('partner','address1','address2','city','phonecode','phone')->with(['City.State.Country'	=>	function($Q){ $Q->select('id','name'); }]); },
					'Logins'	=>	function($Q){ $Q->select('partner','email'); }
				]); },
				'Product'	=>	function($Q){ $Q->select('code','name'); },
				'Edition'	=>	function($Q){ $Q->select('code','name'); }
			])->whereIn('customer',$Codes)->get()->groupBy('customer');
		}
		
		public function update_customers($Code, Request $request){
			$Old = $request->OD; $New = $request->DST;
			$Add = array_values(array_diff((array) $New,(array) $Old)); $Del = array_values(array_diff((array) $Old,(array) $New));
			if(!empty($Del)) foreach($Del as $D) $this->DelCustomerSupportTeam_1($D);
			if(!empty($Add)) foreach($Add as $D) $this->AddCustomerSupportTeam_1($Code,$D);
			return 1;
		}
	
		private function DelCustomerSupportTeam_1($CPE){
			$CPEA = explode("-",$CPE); $this->DelCustomerSupportTeam($CPEA[0],$CPEA[1],$CPEA[2]);
		}
	
		private function AddCustomerSupportTeam_1($TST,$CPE){
			$CPEA = explode("-",$CPE); $this->AddCustomerSupportTeam($TST,$CPEA[0],$CPEA[1],$CPEA[2]);
		}
	
		private function DelCustomerSupportTeam($CUS,$PRD,$EDN){
			$Cond = ['customer'=>$CUS,'product'=>$PRD,'edition'=>$EDN]; $Set = ['status'	=>	'INACTIVE'];
			return \App\Models\CustomerSupportTeam::where($Cond)->update($Set);
		}
	
		private function AddCustomerSupportTeam($TST,$CUS,$PRD,$EDN){
			$Cond = ['customer'	=>	$CUS,'product'	=>	$PRD,'edition'	=>	$EDN]; $Data = ['assigned_by'	=>	$this->getAuthUser()->partner, 'supportteam'	=>	$TST];
			$CST = new \App\Models\CustomerSupportTeam();
			if($CST->where($Cond)->get()->isNotEmpty()) $this->DelCustomerSupportTeam($CUS,$PRD,$EDN);
			return \App\Models\CustomerSupportTeam::create(array_merge($Cond,$Data));
		}
	
		public function detail_search(Request $request){
			$ORM = new \App\Models\Supportteam;//::with('Countries','Products','Editions');
			if($request->supportteam) $ORM = $this->ModifyORMForSearch($ORM,$request->supportteam);
			if($request->country) $ORM = $this->ModifyORMForFilter($ORM,'Country',$request->country);
			if($request->distributor) $ORM = $this->ModifyORMForFilter($ORM,'Distributor',$request->distributor);
			//if($request->product) $ORM = $this->ModifyORMForFilter($ORM,'Product',$request->product);
			//if($request->edition) $ORM = $this->ModifyORMForFilter($ORM,'Edition',$request->edition);
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
						$Q->whereHas('Countries',function($Q) use($Term){ $Q->where('country',$Term); });
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
						$Q->whereHas('Distributors1',function($Q) use($Term){ $Q->where('code',$Term); });
					});
					break;
			}
			return $ORM;
		}

}
