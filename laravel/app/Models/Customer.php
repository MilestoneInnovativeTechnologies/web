<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{

	protected static function boot(){
		parent::boot();
		static::addGlobalScope('active',function(\Illuminate\Database\Eloquent\Builder $builder){
			$builder->where('status','!=','INACTIVE');
		});
		static::addGlobalScope('only',function(\Illuminate\Database\Eloquent\Builder $builder){
			$builder->where(function($Q){ $Q->whereHas('Roles',function($Q){ $Q->where('rolename','customer'); }); });
		});
		static::addGlobalScope('latest',function(\Illuminate\Database\Eloquent\Builder $builder){
			$builder->latest();
		});
		static::addGlobalScope('own',function(\Illuminate\Database\Eloquent\Builder $builder){
			$user = (\Auth()->user()) ?: Auth()->guard('api')->user(); if($user){
				$me = $user->partner;
				if($user->rolename == 'distributor'){
					$builder->where(function($Q) use($me){
						$Q->whereHas('Parent1',function($Q) use($me){ $Q->where('parent',$me)->orWhereHas('Parent1',function($Q) use($me){ $Q->where('parent',$me); }); });
					});
				} elseif($user->rolename == 'supportagent'){
					$myTeam = $user->Parent->parent;
					$builder->where(function($Q) use($myTeam){
						$Q->whereHas('Supportteam',function($Q) use($myTeam){ $Q->where('supportteam',$myTeam); });
					});
				} elseif($user->rolename == 'dealer'){
					$builder->where(function($Q) use($me){
						$Q->whereHas('Parent1',function($Q) use($me){ $Q->where('parent',$me); });
					});
				} elseif($user->rolename == 'supportteam'){
					$builder->where(function($Q) use($me){
						$Q->whereHas('Supportteam',function($Q) use($me){ $Q->where('supportteam',$me); });
					});
				} elseif($user->rolename == 'customer'){
					$builder->where(function($Q)use($me){ $Q->where('code',$me); });
				}
			}
		});
	}

	protected $table = 'partners';
	protected $primaryKey = 'code';
	public $incrementing = false;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['status','status_description','created_by','created_at','updated_at'];
	//protected $visible = ['code','name','Details','Logins','Roles'];
	protected $with = ['Details.City.State.Country','Logins','Roles'];


	public $actions = ['new','panel','edit','presale','resetlogin','changedistributor','add_product'];
	public $conditional_action = [];
	public $role_groups = [[],['company'],['distributor','dealer'],['supportagent','supportteam']];
	public $default_group = 0;
	public $group_actions = [0=>[],1=>[0,1,2,3,4,5,6],2=>[0,1,2,4,5,6],3=>[0,1,2,3,4,5,6]];
	public $modal_actions = ['new'];

	protected function _GETROLEGROUP($rolename){ foreach($this->role_groups as $grp => $names) if(in_array($rolename,$names)) return $grp; return $this->default_group; }
	protected function _GETGROUPACTIONS($group){ return $this->group_actions[$group]; }
	protected function _GETROLEACTIONS($role){ return $this->_GETGROUPACTIONS($this->_GETROLEGROUP($role)); }
	public function _GETARRAYVALUES($array, $keys){ return array_map(function($key)use($array){ return $array[$key]; },$keys); }
	public function _GETAUTHUSER(){ return (Auth()->user())?:(Auth()->guard("api")->user()); }

	public $action_title = ['new' => 'Add new customer','panel' => 'View Details','edit' => 'Edit this customer','presale' => 'Change Presale dates','resetlogin' => 'Send Login reset instructions','changedistributor' => 'Change distributor','changeproduct' => 'Change product and edition','register' => 'Register customer','ondemancategories' => 'Allow/Disallow on demant support categories','add_product' => 'Add another product'];
	public $action_icon = ['new' => 'plus','panel' => 'list-alt','edit' => 'edit','presale' => 'random','resetlogin' => 'log-in','changedistributor' => 'home','changeproduct' => 'cd','register' => 'registration-mark','ondemancategories' => 'tasks','add_product' => 'superscript'];

	protected $appends = ['available_actions'/*,'dealer','distributor'*/];
	public function getAvailableActionsAttribute($value = null){
		$role = $this->_GETAUTHUSER()->rolename;
		$role_actions = $this->_GETROLEACTIONS($role);
		if(!$this->exists) return $this->_GETARRAYVALUES($this->actions,$role_actions);
		$actions = array_filter($role_actions,function($ra){ return ($this->conditional_action && array_key_exists($ra,$this->conditional_action)) ? call_user_func([$this,$this->conditional_action[$ra]],$this) : true; });
		return $this->_GETARRAYVALUES($this->actions,$actions);
	}
	public function getDealerAttribute($value = null){
		return $this->get_dealer();
	}
	public function getDistributorAttribute($value = null){
		return $this->get_distributor();
	}

	public function get_distributor(){
		return \App\Models\Distributor::find(($this->ParentDetails[0]->Roles->contains('name','distributor')) ? $this->ParentDetails[0]->code : $this->ParentDetails[0]->ParentDetails[0]->code);
	}

	public function get_dealer(){
		return ($this->ParentDetails[0]->Roles->contains('name','dealer')) ? $this->ParentDetails[0] : null;
	}

	public function details(){
		return $this->hasOne('App\Models\PartnerDetails','partner','code');
	}

	public function logins(){
		return $this->hasMany('App\Models\PartnerLogin','partner','code');
	}

	public function roles(){
		return $this->hasMany('App\Models\PartnerRole','partner','code');
	}

	public function cookies(){
		return $this->hasMany('App\Models\CustomerCookie','customer','code');
	}

	public function connections(){
		return $this->hasMany('App\Models\CustomerRemoteConnection','customer','code');
	}

	public function supportteam(){
		return $this->hasMany('App\Models\CustomerSupportTeam','customer','code');
	}

	public function parent1(){
		return $this->hasOne('App\Models\PartnerRelation','partner','code');
	}

	public function parentDetails(){
		return $this->belongsToMany('App\Models\Partner','partner_relations','partner','parent')->withoutGlobalScope('active')->with('Roles','ParentDetails');
	}

	public function registration(){
		return $this->hasMany('App\Models\CustomerRegistration','customer','code')->with(['Product' => function($Q){ $Q->select('code','name'); },'Edition' => function($Q){ $Q->select('code','name'); }]);
	}

	public function printobjects(){
		return $this->hasMany('App\Models\CustomerPrintObject','customer','code');
	}

	public function tickets(){
		return $this->hasMany('App\Models\Ticket','customer','code');
	}

	public function forms(){
		return $this->hasMany('App\Models\GeneralUpload','customer','code');
	}

	public function backups(){
		return $this->hasMany('App\Models\DatabaseBackup','customer','code');
	}

	public function uploads(){
		return $this->hasMany('App\Models\GeneralUpload','customer','code');
	}

	public function register(){
		return $this->hasMany('App\Models\CustomerRegistration','customer','code');
	}

	public function industry(){
		return $this->belongsToMany("App\Models\CustomerIndustry","partner_details","partner","industry");
	}

	public function countries(){
		return $this->hasMany('App\Models\PartnerCountries','partner','code');
	}

	public function contracts(){
		//
	}

}
