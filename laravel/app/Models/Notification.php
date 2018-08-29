<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
	
	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('active', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$builder->where(function($Q){ $Q->where('status','ACTIVE'); });
		});
		static::addGlobalScope('latest', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$builder->latest();
		});
		static::addGlobalScope('own', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$user = (\Auth()->user()) ?: Auth()->guard('api')->user();
			if($user && $user->rolename != 'company'){
				$role = $user->rolename; $code = $user->partner;
				$builder->where(function($Q) use($role, $code){
					$Q->where(function($Q) use($code){
						$Q->where('target','public')->where(function($Q) use($code){
							$Q->where('target_type','All')
								->orWhere(function($Q) use($code){
								$Q->where('target_type','Only')->whereHas('Audience',function($Q) use($code){ $Q->where('partner',$code); });
							})
								->orWhere(function($Q) use($code){
								$Q->where('target_type','Except')->whereDoesntHave('Audience',function($Q) use($code){ $Q->where('partner',$code); });
							});
						});
					})->orWhere(function($Q) use($role, $code){
						$Q->where('target',$role)->where(function($Q) use($code){
							$Q->where('target_type','All')
								->orWhere(function($Q) use($code){
									$Q->where('target_type','Only')->whereHas('Audience',function($Q) use($code){ $Q->where('partner',$code); });
								})
								->orWhere(function($Q) use($code){
									$Q->where('target_type','Except')->whereDoesntHave('Audience',function($Q) use($code){ $Q->where('partner',$code); });
								});
						});
					});
				});
			}
		});
	}
	
	protected $table = 'notifications';
	protected $primaryKey = 'code';
	public $incrementing = false;
	public $timestamps = true;
	//protected $fillable = ['code','name','description','class','url','arg1','arg2','arg3','arg4','arg5','arg6','arg7','arg8','arg9'];
	protected $guarded = [];
	//protected $hidden = ['created_at','updated_at'];
	//protected $visible = ['code','name','Details','Logins'/*,'Roles'*/,'Privilage','Defaultst','Customers','Distributors'];
	//protected $with = ['Customer','User'];
	
	public $actions = ['new','audience','edit','preview','serve','list','report'];
	public $conditional_action = [];
	public $role_groups = [[],['company']];
	public $group_actions = [0=>[4,5],1=>[0,1,2,3,6]];
	public $default_group = 0;
	public $modal_actions = ['new'];

	public $action_title = ['new' => 'Create new Notification', 'audience' => 'Manage Audience for this notification', 'edit' => 'Edit notification content', 'preview' => 'Preview this notification', 'report' => 'View the readers'];
	public $action_icon = ['new' => 'plus', 'audience' => 'user', 'edit' => 'edit', 'preview' => 'eye-open', 'report' => 'signal'];

	protected function _GETROLEGROUP($rolename){ foreach($this->role_groups as $grp => $names) if(in_array($rolename,$names)) return $grp; return $this->default_group; }
	protected function _GETGROUPACTIONS($group){ return $this->group_actions[$group]; }
	protected function _GETROLEACTIONS($role){ return $this->_GETGROUPACTIONS($this->_GETROLEGROUP($role)); }
	public function _GETARRAYVALUES($array, $keys){ return array_map(function($key)use($array){ return $array[$key]; },$keys); }
	public function _GETAUTHUSER(){ return (Auth()->user())?:(Auth()->guard("api")->user()); }

	protected $appends = ['available_actions'];
	public function getAvailableActionsAttribute($value = null){
		$AuthUser = $this->_GETAUTHUSER();
		$role = ($AuthUser) ? $AuthUser->rolename : 'public';
		$role_actions = $this->_GETROLEACTIONS($role);
		if(!$this->exists) return $this->_GETARRAYVALUES($this->actions,$role_actions);
		$actions = array_filter($role_actions,function($ra){ return ($this->conditional_action && array_key_exists($ra,$this->conditional_action)) ? call_user_func([$this,$this->conditional_action[$ra]],$this) : true; });
		return $this->_GETARRAYVALUES($this->actions,$actions);
	}
	
	protected function setCodeAttribute($Code = NULL){ $this->attributes['code']	=	($Code)?:$this->NewCode(); }
	public function NewCode(){
		$CodePrefixChar = "NOT";
		$TotalCodeLength = 7;
		$LastNum = 5410; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
		$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
		$LastCode = $this->withoutGlobalScopes()->where($this->primaryKey,"REGEXP",$WhereValue)->max($this->primaryKey);
		if($LastCode) $LastNum = intval(mb_substr($LastCode,$PrefixLength));
		return ($CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT)));
	}
	
	protected $viewable_days = 90;
	public $cookie_name_prefix = '_mit.notify.';
	public $cookie_value = '_y';
	
	
	public function _validation(){
		$Rule = [
			'code'			=>	'nullable|unique:notifications,code',
			'title'		=>	'required',
			'date'			=>	'required',
			'description'			=>	'required',
		];
		$Message = [
			'code.unique'				=>	'The code is already in use.',
			'title.required'	=>	'Title is mandatory field.',
			'description.required'			=>	'Description is a required field.',
			'date.required'			=>	'Please mention a publish date.'
		];
		return ['rules' => $Rule, 'messages' => $Message];
	}
	
	public function audience(){
		return $this->belongsToMany('App\Models\Partner','notification_audiences','notification','partner');
	}
	
	public function serve_anchor(){
		return '<a href="' . $this->serve_link() . '" class="btn btn-link pull-right"> Read >>> </a>';
	}
	
	public function serve_button(){
		return '<a href="' . $this->serve_link() . '" class="btn btn-default btn-sm pull-right">Read</a>';
	}
	
	public function serve_link(){
		return Route('notification.serve',$this->code);
	}
	
	public function scopeNotifiable($Q){
		return $Q->where('notify_from','<=',date('Y-m-d'))->where('notify_to','>=',date('Y-m-d'))->where('date','<=',date('Y-m-d'));
	}
	
	public function scopeViewable($Q){
		return $Q->where('date','>=',date('Y-m-d',strtotime('-'.$this->viewable_days.' days')))->where('date','<=',date('Y-m-d'));
	}
	
}
