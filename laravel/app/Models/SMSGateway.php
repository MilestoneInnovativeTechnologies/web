<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SMSGateway extends Model
{
	
	protected $table = 'sms_gateways';
	protected $primaryKey = 'code';
	public $incrementing = false;
	public $timestamps = true;
	protected $fillable = ['code','name','description','class','url','arg1','arg2','arg3','arg4','arg5','arg6','arg7','arg8','arg9'];
	//protected $guarded = [];
	protected $hidden = ['created_at','updated_at'];
	//protected $visible = ['code','name','Details','Logins'/*,'Roles'*/,'Privilage','Defaultst','Customers','Distributors'];
	//protected $with = ['Customer','User'];
	
	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('active', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$builder->where(function($Q){ $Q->where('status','ACTIVE'); });
		});
	}
	
	public $actions = ['add','edit','inactivate'];
	public $conditional_action = [];
	public $role_groups = [[],['company']];
	public $group_actions = [0=>[],1=>[0,1,2]];
	public $default_group = 0;
	public $modal_actions = ['add'];

	public $action_title = ['add' => 'Add new Gateway','edit' => 'Edit this Gateway','inactivate' => 'Inactivate this gateway'];
	public $action_icon = ['add' => 'plus','edit' => 'edit','inactivate' => 'flash'];

	protected function _GETROLEGROUP($rolename){ foreach($this->role_groups as $grp => $names) if(in_array($rolename,$names)) return $grp; return $this->default_group; }
	protected function _GETGROUPACTIONS($group){ return $this->group_actions[$group]; }
	protected function _GETROLEACTIONS($role){ return $this->_GETGROUPACTIONS($this->_GETROLEGROUP($role)); }
	public function _GETARRAYVALUES($array, $keys){ return array_map(function($key)use($array){ return $array[$key]; },$keys); }
	public function _GETAUTHUSER(){ return (Auth()->user())?:(Auth()->guard("api")->user()); }

	protected $appends = ['available_actions'];
	public function getAvailableActionsAttribute($value = null){
		$role = $this->_GETAUTHUSER()->rolename;
		$role_actions = $this->_GETROLEACTIONS($role);
		if(!$this->exists) return $this->_GETARRAYVALUES($this->actions,$role_actions);
		$actions = array_filter($role_actions,function($ra){ return ($this->conditional_action && array_key_exists($ra,$this->conditional_action)) ? call_user_func([$this,$this->conditional_action[$ra]],$this) : true; });
		return $this->_GETARRAYVALUES($this->actions,$actions);
	}
	
	protected function setCodeAttribute($Code = NULL){ $this->attributes['code']	=	($Code)?:$this->NewCode(); }
	public function NewCode(){
		$CodePrefixChar = "SMSG";
		$TotalCodeLength = 7;
		$LastNum = 0; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
		$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
		$LastCode = $this->withoutGlobalScopes()->where($this->primaryKey,"REGEXP",$WhereValue)->max($this->primaryKey);
		if($LastCode) $LastNum = intval(mb_substr($LastCode,$PrefixLength));
		return ($CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT)));
	}
	
	public function _validation(){
		$Rule = [
			'code'	=>	'nullable|unique:sms_gateways,code',
			'name'	=>	'required'
		];
		$Message = [
			'code.unique'	=>	'The code is already in use.',
			'name.required'	=>	'The name is mandatory field.'
		];
		return ['rules' => $Rule, 'messages' => $Message];
	}
	
	public function fillable_fields(){
		return $this->fillable;
	}
	public function create_new($code,$name,$description,$class,$url,$arg1,$arg2,$arg3,$arg4,$arg5,$arg6,$arg7,$arg8,$arg9){
		return $this->create(compact('code','name','description','class','url','arg1','arg2','arg3','arg4','arg5','arg6','arg7','arg8','arg9'));
	}
	
}
