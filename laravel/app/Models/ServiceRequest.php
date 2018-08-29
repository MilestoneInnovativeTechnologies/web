<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
	protected $table = 'service_requests';
	protected $primaryKey = 'id';
	public $incrementing = true;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['created_at','updated_at'];
	//protected $visible = ['code','name','Details','Logins'/*,'Roles'*/,'Privilage','Defaultst','Customers','Distributors'];
	//protected $with = ['Customer','User'];
		
	
	//public $inactive_activeness_time = '72 hours';
	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('own', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$user = (\Auth()->user()) ?: Auth()->guard('api')->user(); if($user){
				$me = $user->partner;
				if($user->rolename == "supportteam"){
					$builder->where(function($Q) use($me){ $Q->where('supportteam',$me); });
				} elseif($user->rolename == "supportagent") {
					$myTeam = $user->Parent->parent;
					$builder->where(function($Q) use($myTeam){ $Q->where('supportteam',$myTeam); });
				} elseif($user->rolename == "company") {

				} else {
					$builder->where(function($Q) use($me){ $Q->where('user',$me); });
				}
			}
		});
		static::addGlobalScope('active', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$inactive_activeness_time = '72 hours'; $time = strtotime('-'.$inactive_activeness_time);
			$builder->where(function($Q) use($time){ $Q->where('status','ACTIVE')->orWhere(function($Q) use($time){ $Q->where('status','INACTIVE')->where('time','>',$time); }); });
		});
		static::addGlobalScope('latest', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$builder->latest();
		});
	}
	
	public $actions = ['add','edit','delete','respond','response','view_all'];
	public $conditional_action = [1=>'isOwnerNActive',2=>'isOwnerNActive',3=>'isActive',4=>'isRespUpd'];
	public $role_groups = [[],['supportagent','supportteam']];
	public $default_group = 0;
	public $group_actions = [0=>[0,1,2],1=>[3,4]];
	public $list_actions = [1,2,3,4];
	
	protected function _GETROLEGROUP($rolename){ foreach($this->role_groups as $grp => $names) if(in_array($rolename,$names)) return $grp; return $this->default_group; }
	protected function _GETGROUPACTIONS($group){ return $this->group_actions[$group]; }
	protected function _GETROLEACTIONS($role){ return $this->_GETGROUPACTIONS($this->_GETROLEGROUP($role)); }
	public function _GETARRAYVALUES($array, $keys){ return array_map(function($key)use($array){ return $array[$key]; },$keys); }
	public function _GETAUTHUSER(){ return (Auth()->user())?:(Auth()->guard("api")->user()); }

	protected function isOwnerNActive($Model){ return ($Model->user == $this->_GETAUTHUSER()->partner && $this->isActive($Model)); }
	protected function isActive($Model){ return ($Model->status == 'ACTIVE'); }
	protected function isRespUpd($Model){ return ($Model->status == 'INACTIVE' && is_null($Model->ticket)); }
	
	protected $appends = ['available_actions'];
	
	public function getAvailableActionsAttribute($value = null){
		$role = $this->_GETAUTHUSER()->rolename;
		$role_actions = $this->_GETROLEACTIONS($role);
		if(!$this->exists) return $this->_GETARRAYVALUES($this->actions,$role_actions);
		$actions = array_filter($role_actions,function($ra){ return ($this->conditional_action && array_key_exists($ra,$this->conditional_action)) ? call_user_func([$this,$this->conditional_action[$ra]],$this) : true; });
		return $this->_GETARRAYVALUES($this->actions,$actions);
	}
	
	public $action_title = ['add' => 'Add new service request','edit' => 'Edit','delete' => 'Delete','respond' => 'Respond to this Request','response' => 'Update response of this ticket', 'view_all' => 'View resolved requests'];
	public $action_icon = ['add' => 'plus','edit' => 'edit','delete' => 'remove','respond' => 'level-up','response' => 'edit', 'view_all' => 'time'];
	public $ticket_response = 'Support ticket have been created for this service request.';
	

	public function user(){
		return $this->belongsTo('App\Models\Partner','user','code')->select('code','name');
	}

	public function supportteam(){
		return $this->belongsTo('App\Models\SupportTeam','supportteam','code')->select('code','name');
	}

	public function ticket(){
		return $this->belongsTo('App\Models\Ticket','ticket','code');
	}

	public function responder(){
		return $this->belongsTo('App\Models\Partner','responder','code')->with('Roles');
	}
	
	public function validation_rules(){
		$Rules = [
			'supportteam'	=>	'required|exists:partners,code',
			'message'	=>	'required'
		];
		$Messages = [
			'supportteam.required' => 'Please select a Support Team',
			'supportteam.exists' => 'Selected Support Team doesn\'t Exists.',
			'message.required' => 'Message cannot be empty.',
		];
		return ['rules' => $Rules, 'messages' => $Messages];
	}
	
	public function store($supportteam, $message, $user, $status = 'ACTIVE'){
		$user_time = time();
		return $this->create(compact('supportteam','message','user','user_time','status'));
	}
	
	public function add_ticket_response($Ticket, $User){
		$this->ticket = $Ticket->code; $this->save();
		$this->add_response($this->ticket_response,$User->partner,$User->Roles->implode('displayname',', '));
	}
	
	public function add_response($response, $responder, $role){
		$time = time(); $this->update(compact('response','responder','role','time'));
		$this->inactive_srq();
	}
	
	public function inactive_srq(){
		$this->status = 'INACTIVE'; $this->save();
	}
	
}
