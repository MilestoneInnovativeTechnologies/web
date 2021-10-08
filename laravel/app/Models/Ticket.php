<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
	/**
	 * The "booting" method of the model.
	 *
	 * @return void
	 */
	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('latest', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$builder->latest();
		});
		static::addGlobalScope('own', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$user = (\Auth()->user()) ?: \Auth()->guard('api')->user();
			if($user){
				$me = $user->partner;
				if($user->rolename == 'distributor' || $user->rolename == 'dealer'){
					$builder->where(function($Q) use($me){ $Q->where('created_by',$me)/*->orHas('Customer')*/; });
				} elseif($user->rolename == 'supportteam') {
					$builder->where(function($Q) use($me){ $Q->whereHas('Team',function($Q) use($me){ $Q->where('team',$me); }); });
				} elseif($user->rolename == 'supportagent') {
					$myTeam = $user->Parent->parent;
					$builder->where(function($Q) use($myTeam){ $Q->whereHas('Team',function($Q) use($myTeam){ $Q->where('team',$myTeam); }); });
				} elseif($user->rolename == 'customer') {
					$builder->where(function($Q) use($me){ $Q->where('customer',$me); });
				}
			}
			
		});
	}

	protected function setCodeAttribute($Code = NULL){
		$this->attributes['code']	=	($Code)?:$this->NewCode();
	}
	
	private function ALP($N, $MIN = 1, $MAX = 26, $ALP = "ABCDEFGHIJKLMNOPQRSTUVWXYZ", $SIZE = 1){
		$ALPAry = str_split($ALP,$SIZE); $Step = (1+$MAX-$MIN)/count($ALPAry); 
		$Index = intval(round($N/$Step)); return (array_key_exists($Index,$ALPAry))?$ALPAry[$Index]:$ALPAry[array_rand($ALPAry,1)];
	}
	
	public function NewCode(){
		$CodePrefixChar = date("y").$this->ALP(date("W"),1,52).$this->ALP(date("j"),1,31).str_pad(date("z"),3,"0",STR_PAD_LEFT).$this->ALP(date("G"),0,23).$this->ALP(date("i"),0,59).$this->ALP(date("s"),0,59); $TotalCodeLength = 12;
		$LastNum = 0; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
		$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
		$LastCode = $this->withoutGlobalScope('own')->where($this->primaryKey,"REGEXP",$WhereValue)->max($this->primaryKey);//orderBy($this->primaryKey,"desc")->limit(1)->pluck($this->primaryKey);
		if($LastCode) $LastNum = intval(mb_substr($LastCode,$PrefixLength));
		return ($CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT)));
	}
	

	protected $table = 'tickets';
	protected $primaryKey = 'code';
	public $incrementing = false;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['created_at','updated_at'];
	//protected $visible = ['code','name','Details','Logins'/*,'Roles'*/,'Privilage','Defaultst','Customers','Distributors'];
	protected $with = ['Category','Type','Cstatus'];
	public $actions = ['view','edit','delete','entitle','reassign','tasks','communicate','reopen','closure','complete','feedback','recreate','enquire','close','req_complete','force_complete','transcript','dismiss'];
	public $ticket_status = ['NEW','OPENED','INPROGRESS','HOLD','CLOSED','REOPENED','COMPLETED','RECREATED','DISMISSED'];
	public $status_actions = [0=>[0,1,2,3,4,5,17],1=>[0,5,3],2=>[0,5,6,16,3],3=>2,4=>[0,7,8,9,10,14,15,16,3],5=>[0,6,8,11,12,13,16,3],6=>[0,8,16,3],7=>[0,3,8,16,17,3],8=>[0,3,7,10,3]];
	public $handlers_group = [['supportteam','supportagent'],['customer','distributor','dealer']];
	public $public_group = 0;
	public $group_actions = [0=>[0,1,2,3,5,7,8,11,12,14,15,16,17],1=>[0,1,2,6,7,9,10,13]];
	public $conditional_action = [1=>'isUserTheCreator',2=>'isUserTheCreator',14=>'isCrossedMailTime',15=>'isCrossedForceTime',9=>'isNotCustomerThenSelf',10=>'isNotCustomerThenSelf'];
	public $close_to_complete_delay_mail = 1.5 * 24 * 60 * 60;
	public $close_to_complete_delay_force = 3 * 24 * 60 * 60;
	
	protected $appends = ['available_actions','category_specs'];
	protected function _GETROLEGROUP($rolename){ foreach($this->handlers_group as $grp => $names) if(in_array($rolename,$names)) return $grp; return NULL; }
	protected function _GETGROUPACTIONS($group){ return is_null($group) ? array_keys($this->actions) : $this->group_actions[$group]; }
	protected function _GETROLEACTIONS($role){ return ($role == 'supportagent' && $TPvs = \App\Models\TechnicalSupportAgent::find($this->getAuthUser()->partner)->ticket_privilages) ? $this->_GETARRAYKEYS($this->actions,$this->_GETRAWARRAY($TPvs->privilages)) : $this->_GETGROUPACTIONS($this->_GETROLEGROUP($role)); }
	protected function _GETSTATUSACTIONS($status){ $status_index = array_search($status,$this->ticket_status); return(is_array($this->status_actions[$status_index])) ? $this->status_actions[$status_index] : $this->_GETSTATUSACTIONS($this->ticket_status[$this->status_actions[$status_index]]); }
	protected function _GETARRAYVALUES($array, $keys){ return array_map(function($key)use($array){ return $array[$key]; },$keys); }
	protected function _GETRAWARRAY($actionraw){ return explode('-',mb_substr($actionraw,1,-1)); }
	protected function _GETARRAYKEYS($array, $values){ return array_map(function($value)use($array){ return array_search($value,$array); },$values); }
	protected function getAuthUser(){ return (Auth()->user())?:(Auth()->guard("api")->user()); }
	protected function isUserTheCreator($Obj){ return $Obj->created_by == $this->getAuthUser()->partner; }
	protected function isCrossedMailTime($Obj){ return $Obj->Cstatus->start_time <= time() - $this->close_to_complete_delay_mail; }
	protected function isCrossedForceTime($Obj){ return $Obj->Cstatus->start_time <= time() - $this->close_to_complete_delay_force; }
	protected function isNotCustomerThenSelf($Obj){ if($this->getAuthUser()->rolename == 'customer') return true; return ($Obj->created_by == $this->getAuthUser()->partner); }
	
	public function getAvailableActionsAttribute($value = null){
		if(!$this->status || !$this->Cstatus) return null;
		$role = $this->getAuthUser()->rolename; $status = $this->Cstatus->status;
		$actions = array_values(array_intersect($this->_GETROLEACTIONS($role),$this->_GETSTATUSACTIONS($status)));
		$filter_condition = $this->conditional_action;
		return $this->_GETARRAYVALUES($this->actions,array_filter($actions,function($action)use($filter_condition){ if(!array_key_exists($action,$filter_condition)) return true; return $this->{$filter_condition[$action]}($this); }));
	}
	
	public function getCategorySpecsAttribute($value = null){
		if(!$this->category) return null;
		return \App\Models\TicketCategory::where(['ticket' => $this->code, 'category' => $this->category])->get();
	}
	
	public function get_responders(){
		if(!$this->Tasks || $this->Tasks->isEmpty()) return collect([]);
		return $this->Tasks->map(function($item){ return ($item->Responder && $item->Responder->Responder) ? $item->Responder->Responder->name : null; })->filter()->unique();
	}
	
	public function category(){
		return $this->belongsTo('App\Models\TicketCategoryMaster','category','code')->withoutGlobalScopes();//->select('code','name','priority');
	}
	
	public function customer(){
		return $this->belongsTo('App\Models\Customer','customer','code')->select('code','name');
	}
	
	public function product(){
		return $this->belongsTo('App\Models\Product','product','code')->select('code','name');
	}
	
	public function edition(){
		return $this->belongsTo('App\Models\Edition','edition','code')->select('code','name');
	}
	
	public function type(){
		return $this->belongsTo('App\Models\TicketType','ticket_type','code')->select('code','name');
	}
	
	public function team(){
		return $this->hasOne('App\Models\TicketSupportTeam','ticket','code');
	}
	
	public function cstatus(){
		return $this->hasOne('App\Models\TicketCurrentStatus','ticket','code');
	}
	
	public function status(){
		return $this->hasMany('App\Models\TicketStatusTrans','ticket','code');
	}
	
	public function tasks(){
		return $this->hasMany('App\Models\TicketTask','ticket','code');
	}
	
	public function closure(){
		return $this->hasOne('App\Models\TicketClosure','ticket','code');
	}
	
	public function feedback(){
		return $this->hasOne('App\Models\TicketFeedback','ticket','code');
	}
	
	public function createdby(){
		return $this->belongsTo('App\Models\Partner','created_by','code')->select('code','name');
	}
	
	public function cookies(){
		return $this->hasMany('App\Models\CustomerCookie','customer','customer');
	}
	
	public function connections(){
		return $this->hasMany('App\Models\CustomerRemoteConnection','customer','customer');
	}
	
	public function pobjects(){
		return $this->hasMany('App\Models\CustomerPrintObject','customer','customer');
	}
	
	public function conversations(){
		return $this->hasMany('App\Models\TicketCoversation','ticket','code')->with(['User' => function($Q){ $Q->select('code','name'); }]);
	}
	
	public function uploads(){
		return $this->hasMany('App\Models\GeneralUpload','ticket','code');
	}
	
	public function attachments(){
		return $this->hasMany('App\Models\TicketAttachment','ticket','code');
	}
	
	public function category_specs_values(){
		return $this->hasMany('App\Models\TicketCategory','ticket','code');
	}
}
