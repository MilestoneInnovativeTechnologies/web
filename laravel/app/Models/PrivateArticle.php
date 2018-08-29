<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateArticle extends Model
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
						$Q->where('target','partner')->where(function($Q) use($code){
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
	
	protected $table = 'private_articles';
	protected $primaryKey = 'code';
	public $incrementing = false;
	public $timestamps = true;
	//protected $fillable = ['code','name','description','class','url','arg1','arg2','arg3','arg4','arg5','arg6','arg7','arg8','arg9'];
	protected $guarded = [];
	//protected $hidden = ['created_at','updated_at'];
	//protected $visible = ['code','name','Details','Logins'/*,'Roles'*/,'Privilage','Defaultst','Customers','Distributors'];
	//protected $with = ['Customer','User'];
	
	public $actions = ['new','edit','audience','preview','serve','report'];
	public $conditional_action = [];
	public $role_groups = [[],['company']];
	public $group_actions = [0=>[4],1=>[0,1,2,3,5]];
	public $default_group = 0;
	public $modal_actions = ['new'];

	public $action_title = ['new' => 'Create new Private Article', 'edit' => 'Edit this Article', 'audience' => 'Select audience for this article', 'preview' => 'Preview this article', 'serve' => 'Read this article', 'report' => 'Viewers list'];
	public $action_icon = ['new' => 'plus', 'edit' => 'edit', 'audience' => 'user', 'preview' => 'eye-open', 'serve' => 'book', 'report' => 'signal'];

	protected function _GETROLEGROUP($rolename){ foreach($this->role_groups as $grp => $names) if(in_array($rolename,$names)) return $grp; return $this->default_group; }
	protected function _GETGROUPACTIONS($group){ return $this->group_actions[$group]; }
	protected function _GETROLEACTIONS($role){ return $this->_GETGROUPACTIONS($this->_GETROLEGROUP($role)); }
	public function _GETARRAYVALUES($array, $keys){ return array_map(function($key)use($array){ return $array[$key]; },$keys); }
	public function _GETAUTHUSER(){ return (Auth()->user())?:(Auth()->guard("api")->user()); }

	protected $appends = ['available_actions','url'];
	public function getAvailableActionsAttribute($value = null){
		$AuthUser = $this->_GETAUTHUSER(); if(!$AuthUser) return [];
		$role = $this->_GETAUTHUSER()->rolename;
		$role_actions = $this->_GETROLEACTIONS($role);
		if(!$this->exists) return $this->_GETARRAYVALUES($this->actions,$role_actions);
		$actions = array_filter($role_actions,function($ra){ return ($this->conditional_action && array_key_exists($ra,$this->conditional_action)) ? call_user_func([$this,$this->conditional_action[$ra]],$this) : true; });
		return $this->_GETARRAYVALUES($this->actions,$actions);
	}
	public function getUrlAttribute($value = null){
		return route('article.serve.private',$this->code);
	}
	
	protected function setCodeAttribute($Code = NULL){ $this->attributes['code']	=	($Code)?:$this->NewCode(); }
	public function NewCode(){
		$CodePrefixChar = "PRA";
		$TotalCodeLength = 8;
		$LastNum = 25523; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
		$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
		$LastCode = $this->withoutGlobalScopes()->where($this->primaryKey,"REGEXP",$WhereValue)->max($this->primaryKey);
		if($LastCode) $LastNum = intval(mb_substr($LastCode,$PrefixLength));
		return ($CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT)));
	}
	
	public function _validation(){
		$Rule = [
			'code'			=>	'nullable|unique:public_articles,code',
			'name'		=>	'required',
			'title'			=>	'required',
			'view'			=>	'required',
		];
		$Message = [
			'code.unique'				=>	'The code is already in use.',
			'name.required'	=>	'Subject is mandatory field.',
			'title.required'			=>	'Title is mandatory field.',
			'view.required'			=>	'View name is mandatory field.'
		];
		return ['rules' => $Rule, 'messages' => $Message];
	}

	public function create_new($code,$name,$title,$view){
		return $this->create(compact('code','name','title','view'));
	}
	
	public function audience(){
		return $this->belongsToMany('App\Models\Partner','private_article_audiences','article','partner');
	}



}
