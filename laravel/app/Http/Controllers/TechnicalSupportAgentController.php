<?php

namespace App\Http\Controllers;

use App\Models\TechnicalSupportAgent;
use Illuminate\Http\Request;

class TechnicalSupportAgentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
			return view('tsa.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
			return view('tsa.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $Request)
    {
			$Rules = $this->NewAgentValidation();
			$Validate = \Validator::make($Request->all(),$Rules[0],$Rules[1]);
			if($Validate->fails()) return redirect()->back()->withInput()->withErrors($Validate);
			
			if($Request->parent) { $Details = $this->getPartnerDetails($Request->parent); }
			elseif($this->getAuthUser()->Roles->contains('name','supportteam')){ $Details = $this->getPartnerDetails($this->getAuthUser()->partner); }
			else { return redirect()->back()->with(["info"=>true,"type"=>"danger","text"=>"Cant get parent details."]); }
			
			$SupportAgent = $this->NewAgent($Request,$Details);
			$this->SetAllPrivilages($SupportAgent->code);
			
			return redirect()->route('tsa.tkt.prv',$SupportAgent->code)->with(["info"=>true,"type"=>"success","text"=>"Agent, ".$Request->name.", created successfully."]);
			//return view('tsa.index')->with(["info"=>true,"type"=>"success","text"=>"Agent, ".$Request->name.", created successfully."]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\App\Models\TechnicalSupportAgent  $technicalSupportAgent
     * @return \Illuminate\Http\Response
     */
    public function show($Code)
    {
			$Agent = TechnicalSupportAgent::find($Code);
			//return $Agent->Departments->pluck('Department')->implode('name',', ');
			return view('tsa.show',compact('Agent'));//TechnicalSupportAgent::find($Code);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\App\Models\TechnicalSupportAgent  $technicalSupportAgent
     * @return \Illuminate\Http\Response
     */
    public function edit($Code)
    {
			$Update = true;
			$Agent = TechnicalSupportAgent::find($Code);
			return view('tsa.form',compact('Update','Agent'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\App\Models\TechnicalSupportAgent  $technicalSupportAgent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $Code)
    {
      $Agent = TechnicalSupportAgent::find($Code);
			if($Agent->name != $request->name) $Agent->update(['name'=>$request->name]);
			$Login = $Agent->Logins()->whereHas('Roles',function($Q){ $Q->whereRolename('supportagent'); })->first();
			if($Login->email != $request->email) $Login->update(['email'	=>	$request->email]);
			if($Agent->Details->phone != $request->phone) { $Agent->Details->phone = $request->phone; $Agent->push(); }
			if($Agent->Team->parent != $request->parent) { $Agent->Team->parent = $request->parent; $Agent->push(); }
			$this->AgentDetailsUpdate($Code,$request);
			return view('tsa.index')->with(["info"=>true,"type"=>"success","text"=>"Agent, ".$request->name.", updated successfully."]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\App\Models\TechnicalSupportAgent  $technicalSupportAgent
     * @return \Illuminate\Http\Response
     */
    public function destroy(TechnicalSupportAgent $technicalSupportAgent)
    {
        //
    }

    public function delete(TechnicalSupportAgent $tsa)
    {
			//return $tsa;
      $tsa->status = 'INACTIVE'; $tsa->save();
			return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Agent, ".$tsa->name." deleted successfully."]);
    }
	
	public function login_reset(TechnicalSupportAgent $tsa){
		$Logins = $tsa->Logins[0];
		$pArr = ['id','partner','email','expiry'];
		$vArr = [$Logins->id,$Logins->partner,$Logins->email,strtotime("+18 Hours")];
		$Code = \App\Http\Controllers\KeyCodeController::Encode($pArr,$vArr);
		$this->SendMail('PartnerLoginSetup',$tsa,$Code,$tsa->Logins[0]->email);
		//Mail::queue(new \App\Mail\PartnerLoginSetup($tsa,$Code));
		return redirect()->route('tsa.index')->with(["info"=>true,"type"=>"success","text"=>"Login reset mail have successfully mailed to ".$tsa->name."."]);
	}

	private function NewAgentValidation(){
		$Rules = [
			"name"						=>	"required",
			"email"						=>	"required|email|unique:partner_logins,email",
		];
		$Messages = [
			"name.required"			=>	"The Name field cannot be empty.",
			"email.required"		=>	"Email is Mandatory, Please fill.",
			"email.email"				=>	"Email doesn't seems to be a valid one",
			"email.unique"			=>	"Email is already in use.",
		];
		return [$Rules,$Messages];
	}
	
	private function NewAgent($request, $Details){
		$request->merge(['created_by'=>$this->getAuthUser()->partner]);
		$Partner = TechnicalSupportAgent::create(array_merge(['code'=>null],$request->only('name','created_by')));
		$this->NewAgentDetails($Partner,$Details,$request);
		$this->NewAgentCountry($Partner,$Details->City->State->country);
		$this->NewAgentEmail($Partner,$request);
		$this->NewAgentRelation($Partner, $Details->partner);
		return $Partner;
	}
	
	private function getAuthUser(){
		return (Auth()->user())?:(Auth()->guard("api")->user());
	}
	
	private function NewAgentDetails($Partner, $Details, $Request){
		$Ary = ['code'=>null,'city'=>$Details->city,'state'=>$Details->state,'phonecode'=>$Details->phonecode,'currency'=>$Details->currency,'website'=>$Details->website,'address1'=>$Details->address1,'address2'=>$Details->address2,'phone'=>$Request->phone];
		return $Partner->Details()->create($Ary);
	}
	
	private function getPartnerDetails($Partner){
		return \App\Models\PartnerDetails::wherePartner($Partner)->first();
	}
	
	private function NewAgentCountry($Partner, $Country){
		return \App\Models\PartnerCountries::create(['partner'	=>	$Partner->code, 'country'	=>	$Country]);
	}
	
	private function NewAgentEmail($Partner, $Request){
		$Login = $Partner->Logins()->create(['email'	=>	$Request->email, 'created_by'	=>	$Request->created_by]);
		$RoleCode = \App\Models\Role::whereName('supportagent')->first()->code;
		return $Login->Roles()->create(['partner'	=>	$Partner->code, 'role'	=>	$RoleCode, 'created_by'	=> $Request->created_by]);
	}
	
	private function NewAgentRelation($Partner, $Parent){
		return $Partner->Team()->create(['parent'	=>	$Parent]);
	}
	
	private function AgentDetailsUpdate($Code, $request){
		$Parent = $request->parent; if(!$Parent) return;
		$RD = $this->getPartnerDetails($Parent);
		$MD = $this->getPartnerDetails($Code);
		$CheckList = ['address1','address2','city','state','phonecode','currency','website'];
		foreach($CheckList as $Item) if($RD->$Item != $MD->$Item) $MD->$Item = $RD->$Item;
		$MD->save();
	}
	
	public function list_departments(){
		return view('tsa.departments');
	}
	
	public function update_departments(Request $request){
		TechnicalSupportAgent::all()->each(function($item)use($request){
			$item->Departments()->delete(); $Code = $item->code;
			if($request->$Code){
				$UPDArray = [];
				foreach($request->$Code as $Dept) array_push($UPDArray,['department'	=>	$Dept, 'assigned_by'	=>	\Auth()->user()->partner]);
				$item->Departments()->createMany($UPDArray);
			}
		});
		return redirect()->back()->with(['info'=>true,'type'=>'success','text'=>'Modifications applied.']);
	}
	
	public function ticket_privilages($tsa){
		$TeamActions = $this->GetTeamActions();
		$Agent = TechnicalSupportAgent::whereCode($tsa)->with('ticket_privilages')->first();
		return view('tsa.tktprv',compact('Agent','TeamActions'));
	}
	
	public function update_ticket_privilages($tsa, Request $request){
		$this->SetAgentPrivilages($tsa,$request->$tsa);
		return redirect()->route('tsa.index')->with(["info"=>true,"type"=>"success","text"=>"Privilages Updated successfully."]);
	}
	
	private function GetTeamActions(){
		$Tkt = new \App\Models\Ticket(); $AgentGroup = false; $actions = $Tkt->actions;
		foreach($Tkt->handlers_group as $Grp => $Roles) if($AgentGroup === false && in_array('supportteam',$Roles)) $AgentGroup = $Grp;
		return array_map(function($key)use($actions){ return $actions[$key]; },$Tkt->group_actions[$AgentGroup]);
	}
	
	private function SetAllPrivilages($tsa){
		$TeamAction = $this->GetTeamActions();
		$this->SetAgentPrivilages($tsa,$TeamAction);
	}
	
	private function SetAgentPrivilages($agent, $privilages){
		$privilages = (is_array($privilages) && !empty($privilages)) ? '-' . implode('-',$privilages) . '-' : '--';
		\App\Models\AgentTicketPrivilage::updateOrCreate(['agent' => $agent],['privilages' => $privilages]);
	}

    public function detail_search(Request $request){
        $ORM = new \App\Models\TechnicalSupportAgent;//::with('Countries','Products','Editions');
        if($request->supportagent) $ORM = $this->ModifyORMForSearch($ORM,$request->supportagent);
        if($request->country) $ORM = $this->ModifyORMForFilter($ORM,'Country',$request->country);
        if($request->supportteam) $ORM = $this->ModifyORMForFilter($ORM,'Team',$request->supportteam);
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
            case 'Team':
                $ORM = $ORM->where(function($Q) use($Term){
                    $Q->whereHas('Team',function($Q) use($Term){ $Q->where('parent',$Term); });
                });
                break;
        }
        return $ORM;
    }

    private function SendMail($Mail,$TSA,$Code,$To){
        $Class = '\\App\\Mail\\' . $Mail;
        \App\Libraries\Mail::init()->queue(new $Class($TSA,$Code))->send($To);
    }
}
