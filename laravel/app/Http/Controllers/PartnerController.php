<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

use Validator;

class PartnerController extends Controller
{
	
		public function __construct(){
			
			
		}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      	//$Data = Partner::with('Details.City.State.Country','Logins','Roles')->latest()->paginate(15);
        return view('partner.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
			return view('partner.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
			$RM = $this->NewPartnerRulesAndMessages();
			$Validate = Validator::make($request->all(),$RM[0],$RM[1]);
			if($Validate->fails()) return redirect()->back()->withInput()->withErrors($Validate);
			$Partner = $this->NewPartner($request->code, $request->name);
			$request->merge(['code'	=>	$this->NewPartnerDetailCode()]);
			$Details = $this->NewPartnerDetails($Partner,$request,['code','currency','phonecode','phone']);
			$this->AttachPartnerCountry($Partner, $request->country);
			$Login = $this->NewPartnerLogin($Partner, $request->email);
			$this->AddNewLoginRole($Login, $request->role);
			$this->AddPartnerParent($Partner, Auth()->user()->partner);
      return redirect()->action("PartnerController@index")->with(["info"=>true,"type"=>"success","text"=>"Partner, ".$request->name." added successfully"]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function show(Partner $Partner)
    {
        return view('partner.show',compact('Partner'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function edit(Partner $partner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Partner $partner)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function destroy(Partner $partner)
    {
        //
    }
	
	private function NewPartnerRulesAndMessages(){
		$Rules = [
			'code'	=>	'required|unique:partners',
			'name'	=>	'required',
			'country'	=>	'required|exists:countries,id',
			'email'	=>	'required|email|unique:partner_logins,email',
			'role'	=>	'required|exists:roles,code'
		];
		$Messages = [
			'code.required'	=>	'Partner Code is Mandatory, Please fill.',
			'code.unique'	=>	'Partner Code provided is already taken, please use any other.',
			'name.required'	=>	'Name is a required field.',
			'country.required'	=>	'Country is a required field.',
			'country.exists'	=>	'Country selected is not available in our records.',
			'email.required'	=>	'Email is a mandatory field.',
			'email.email'	=>	'The email provided doesn\'t seems to be a valid one.',
			'email.unique'	=>	'The email you provided is already there in our records. Please choose another email.',
			'role.required'	=>	'Role is a required fields.',
			'role.exists'	=>	'Role selected is not available in our records.'
		];
		return [$Rules, $Messages];
	}
	
	private function NewPartner($Code, $Name){
		return Partner::create(['code'	=>	$Code, 'name'	=>	$Name, 'created_by'	=>	Auth()->user()->partner]);
	}
	
	private function NewPartnerDetails($Partner,$Request,$Flds){
		return $Partner->Details()->create($Request->only($Flds));
	}
	
	private function NewPartnerDetailCode(){
		return (new \App\Models\PartnerDetails())->NextCode();
	}
	
	private function AttachPartnerCountry($Partner, $Country){
		return $Partner->Countries()->attach($Country);
	}
	
	private function NewPartnerLogin($Partner, $Email){
		return $Partner->Logins()->create(['email'	=>	$Email, 'created_by'	=>	Auth()->user()->partner]);
	}
	
	private function AddNewLoginRole($Login, $Role){
		return $Login->Roles()->create(['role'	=>	$Role, 'created_by'	=>	Auth()->user()->partner]);
	}
	
	private function AddPartnerParent($Partner, $Parent){
		$Partner->Parent()->create(['parent'	=> $Parent]);
	}
	
	public function slsl($Partner){
		$Partner = Partner::whereCode($Partner)->with('Logins')->first();
		$Logins = $Partner->Logins[0];
		$pArr = ['id','partner','email','expiry'];
		$vArr = [$Logins->id,$Partner->code,$Logins->email,strtotime("+18 Hours")];
		$Code = (new \App\Http\Controllers\KeyCodeController())->KeyEncode($pArr,$vArr);
		\App\Libraries\Mail::init()->queue(new \App\Mail\PartnerLoginSetup($Partner,$Code))->to($Partner)->send();
		return [$Partner->code, $Partner->name, $Logins->email];
	}
	
	public function detail_search(Request $request){
		$ORM = new \App\Models\Partner;
		if($request->partner) $ORM = $this->ModifyORMForSearch($ORM,$request->partner);
		if($request->dealer) $ORM = $this->ModifyORMForFilter($ORM,'Dealer',$request->dealer);
		elseif($request->distributor) $ORM = $this->ModifyORMForFilter($ORM,'Distributor',$request->distributor);
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
			case 'Dealer':
				$ORM = $ORM->where(function($Q) use($Term){
					$Q->whereHas('Parent',function($Q) use($Term){ $Q->where('parent',$Term); });
				});
				break;
			case 'Distributor':
				$ORM = $ORM->where(function($Q) use($Term){
					$Q->whereHas('Parent',function($Q) use($Term){ $Q->where('parent',$Term)->orWhereHas('Parent1',function($Q) use($Term){ $Q->where('parent',$Term); }); });
				});
				break;
			case 'Country':
				$ORM = $ORM->where(function($Q) use($Term){
					$Q->whereHas('Details.City.State',function($Q) use($Term){ $Q->where('country',$Term); });
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
