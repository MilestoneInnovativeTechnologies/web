<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
	protected static function boot(){
		parent::boot();
		static::addGlobalScope('only',function(\Illuminate\Database\Eloquent\Builder $builder){
			$builder->where(function($Q){ $Q->whereHas('Roles',function($Q){ $Q->where('rolename','distributor'); }); });
		});
		static::addGlobalScope('latest',function(\Illuminate\Database\Eloquent\Builder $builder){
			$builder->latest();
		});
		static::addGlobalScope('own',function(\Illuminate\Database\Eloquent\Builder $builder){
			$user = (\Auth()->user()) ?: Auth()->guard('api')->user();
			if($user){
				$me = $user->partner;
				if($user->rolename == 'distributor'){
					$builder->where(function($Q) use($me){ $Q->where('code',$me); });
				} elseif($user->rolename == 'dealer'){
					$distributor = $user->Parent->parent;
					$builder->where(function($Q) use($distributor){ $Q->where('code',$distributor); });
				} elseif($user->rolename == 'customer'){
					$ParentRoles = $user->Parent->Roles;
					if($ParentRoles->contains('rolename','distributor')) $distributor = $user->Parent->parent;
					else $distributor = $user->Parent->Parent1->parent;
					$builder->where(function($Q) use($distributor){ $Q->where('code',$distributor); });
				} elseif($user->rolename == 'supportteam'){
					$builder->where(function($Q) use($me){ $Q->whereHas('Supportteam',function($Q) use($me){ $Q->where('supportteam',$me); }); });
				} elseif($user->rolename == 'supportagent'){
					$myTeam = $user->Parent->parent;
					$builder->where(function($Q) use($myTeam){ $Q->whereHas('Supportteam',function($Q) use($myTeam){ $Q->where('supportteam',$myTeam); }); });
				}
			}
		});
			
//			
//			if($user->rolename == 'distributor')){
//				$builder->where('code',$user->partner);
//			} elseif($user->rolename == 'dealer')){
//				$builder->where('code',$user->Parent->parent);
//			} elseif($user->rolename == 'customer')){
//				if($user->Parent->Roles->contains('rolename','distributor')) $builder->where('code',$user->Parent->parent);
//				else $builder->where('code',$user->Parent->Parent1->parent);
//			}
//			
			
	}
	protected $table = 'partners';
	protected $primaryKey = 'code';
	public $incrementing = false;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['status','status_description','created_by','created_at','updated_at'];
	//protected $visible = ['code','name','Details','Logins','Roles'];
	protected $with = ['Details.City.State.Country','Details.Pricelist','Logins','Roles'];

	
	public $actions = ['products','countries','panel','edit','transactions','create','resetlogin','supportteam','delete','support_categories','edit_detail','contact_options'];
	public $conditional_action = [];
	public $role_groups = [[],['company'],['supportagent','supportteam']];
	public $default_group = 0;
	public $group_actions = [0=>[],1=>[0,1,2,3,4,5,6,7,9,11],2=>[10,6]];
	public $modal_actions = ['create'];
	
	protected function _GETROLEGROUP($rolename){ foreach($this->role_groups as $grp => $names) if(in_array($rolename,$names)) return $grp; return $this->default_group; }
	protected function _GETGROUPACTIONS($group){ return $this->group_actions[$group]; }
	protected function _GETROLEACTIONS($role){ return $this->_GETGROUPACTIONS($this->_GETROLEGROUP($role)); }
	public function _GETARRAYVALUES($array, $keys){ return array_map(function($key)use($array){ return $array[$key]; },$keys); }
	public function _GETAUTHUSER(){ return (Auth()->user())?:(Auth()->guard("api")->user()); }
	
	public $action_title = ['products' => 'Manage Products','countries' => 'Manage Countries','panel' => 'View Panel','edit' => 'Edit Distributor Details','transactions' => 'Manage Transactions', 'create' => 'Create New Distributor','resetlogin'  => 'Reset distributor logins','supportteam' => 'Change Support Team', 'delete' => 'Delete this distributor', 'support_categories' => 'Assign/Unassign support categories','edit_detail' => 'Edit distributor details', 'contact_options' => 'Modify distributor or customers contact options'];
	public $action_icon = ['products' => 'cd','countries' => 'map-marker','panel' => 'list-alt','edit' => 'edit','transactions' => 'usd','create' => 'plus','resetlogin'  => 'log-in','supportteam' => 'headphones', 'delete' => 'remove', 'support_categories' => 'indent-left', 'edit_detail' => 'edit', 'contact_options' => 'earphone'];
	
	protected $appends = ['available_actions'];
	public function getAvailableActionsAttribute($value = null){
		$role = $this->_GETAUTHUSER()->rolename;
		$role_actions = $this->_GETROLEACTIONS($role);
		if(!$this->exists) return $this->_GETARRAYVALUES($this->actions,$role_actions);
		$actions = array_filter($role_actions,function($ra){ return ($this->conditional_action && array_key_exists($ra,$this->conditional_action)) ? call_user_func([$this,$this->conditional_action[$ra]],$this) : true; });
		return $this->_GETARRAYVALUES($this->actions,$actions);
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
	
	public function childs(){
		return $this->hasMany('App\Models\PartnerRelation','parent','code');
	}
	
	public function dealers(){
		return $this->childs()->with('PartnerRoles','ChildDetails')->whereHas('PartnerRoles',function($Q){ $Q->whereRolename('dealer'); });
	}
	
	public function customers(){
		return $this->childs()->with('PartnerRoles','ChildDetails','Registration')->whereHas('PartnerRoles',function($Q){ $Q->whereRolename('customer'); });
	}
	
	public function dealerCustomers(){
		return $this->dealers()->with(['Children' => function($Q){ $Q->with('ChildDetails','Registration'); }]);
	}
	
	public function scopeWithDealers($Q){
		$Q->with(['Dealers.Croles'	=>	function($Q){
			$Q->where(['rolename'=>'dealer','status'=>'ACTIVE']);
		}]);
	}
	
	public function scopeWithCustomers($Q){
		$Q->with(['Customers.Croles'	=>	function($Q){
			$Q->where(['rolename'=>'customer','status'=>'ACTIVE']);
		}]);
	}
	
	public function get_all_customers(){
		$code = $this->code;
		return $this->whereCode($code)->with(['Childs' => function($Q){ $Q->with(['PartnerRoles','ChildDetails','Children.ChildDetails']); }])->first()->Childs->map(function($item){ return ($item->PartnerRoles->contains('rolename','customer')) ? $item->ChildDetails : ($item->Children->isNotEmpty() ? $item->Children->map(function($item){ return $item->ChildDetails; }) : null); })->filter()->flatten()->values();
  }
	
	public function get_all_dealers(){
		$code = $this->code;
		return $this->whereCode($code)->with(['Childs' => function($Q){ $Q->with(['PartnerRoles','ChildDetails']); }])->first()->Childs->map(function($item){ return ($item->PartnerRoles->contains('rolename','dealer')) ? $item->ChildDetails : null; })->filter()->flatten()->values();
  }
	
	public function get_my_customers(){
		$code = $this->code;
		return $this->whereCode($code)->with(['Childs' => function($Q){ $Q->with(['PartnerRoles','ChildDetails']); }])->first()->Childs->map(function($item){ return ($item->PartnerRoles->contains('rolename','customer')) ? $item->ChildDetails : null; })->filter()->flatten()->values();
  }

	public function countries() {
		return $this->belongsToMany("App\Models\Country","partner_countries","partner","country");
	}
		
	public function products(){
		return $this->belongsToMany("App\Models\Product","partner_products","partner","product")
			->withPivot('edition')
			->where('status','ACTIVE')
			->select('code','name')
			->with(['Editions'	=>	function($Q){ $Q->select('code','name'); }]);
	}
		
	public function editions(){
		return $this->belongsToMany("App\Models\Edition","partner_products","partner","edition")
			->withPivot('product')
			->where('status','ACTIVE')
			->select('code','name')
			->with(['Products'	=>	function($Q){ $Q->select('code','name'); }]);
	}
		
	public function transactions(){
		return $this->hasMany("App\Models\Transaction","distributor","code")->whereStatus('ACTIVE')->oldest('date');
	}
		
	public function supportteam(){
		return $this->hasMany("App\Models\DistributorSupportTeam","distributor","code");
	}
		
	public function tickets(){
		return $this->hasMany("App\Models\Ticket","created_by","code");
	}
		
	public function excludedCategories(){
		return $this->hasMany("App\Models\DistributorExcludeCategory","distributor",'code');
	}
		
	public function contactMethods(){
		return $this->hasOne("App\Models\DistributorContactMethod","distributor",'code');
	}
		
	public function customerContactMethods(){
		return $this->hasOne("App\Models\DistributorCustomerContactMethod","distributor",'code')->whereNull('customer');
	}
		
	public function customersContactMethods(){
		return $this->hasMany("App\Models\DistributorCustomerContactMethod","distributor",'code')->whereNotNull('customer');
	}
	
}
