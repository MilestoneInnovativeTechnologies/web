<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mail extends Model
{
	
	protected $table = 'mails';
	protected $primaryKey = 'code';
	public $incrementing = false;
	public $timestamps = true;
	//protected $fillable = ['code','name','description','class','url','arg1','arg2','arg3','arg4','arg5','arg6','arg7','arg8','arg9'];
	protected $guarded = [];
	//protected $hidden = ['created_at','updated_at'];
	//protected $visible = ['code','name','Details','Logins'/*,'Roles'*/,'Privilage','Defaultst','Customers','Distributors'];
	//protected $with = ['Customer','User'];
	
	public $actions = ['compose','edit','send','report'];
	public $conditional_action = [];
	public $role_groups = [[],['company'],['supportteam','supportagent']];
	public $group_actions = [0=>[],1=>[0,1,2,3],[0,1,2]];
	public $default_group = 0;
	public $modal_actions = ['compose'];

	public $action_title = ['compose' => 'Create new E-Mail', 'edit' => 'Edit this message', 'send' => 'Send this message', 'report' => 'View the mail sent and delivery reports'];
	public $action_icon = ['compose' => 'plus', 'edit' => 'edit', 'send' => 'share', 'report' => 'stats'];

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
		$CodePrefixChar = "MWM";
		$TotalCodeLength = 10;
		$LastNum = 645154; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
		$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
		$LastCode = $this->withoutGlobalScopes()->where($this->primaryKey,"REGEXP",$WhereValue)->max($this->primaryKey);
		if($LastCode) $LastNum = intval(mb_substr($LastCode,$PrefixLength));
		return ($CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT)));
	}
	
	public function _validation(){
		$Rule = [
			'code'			=>	'nullable|unique:mails,code',
			'subject'		=>	'required',
			'body'			=>	'required',
		];
		$Message = [
			'code.unique'				=>	'The code is already in use.',
			'subject.required'	=>	'Subject is mandatory field.',
			'body.required'			=>	'Body is mandatory field.'
		];
		return ['rules' => $Rule, 'messages' => $Message];
	}

	public function create_new($code,$subject,$body){
		return $this->create(compact('code','subject','body'));
	}
	
}
