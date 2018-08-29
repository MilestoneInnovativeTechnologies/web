<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerRegistration extends Model
{
	
	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('active', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$builder->where(function($Q){ $Q->where('status','ACTIVE'); });
		});
	}

	protected $fillable = array('customer', "seqno", 'product', "edition", "presale_enddate", "lic_file", "version", "database", "serialno", "product_id", "key", "registered_on", "created_by", "presale_extended_to", "presale_extended_by");
	public $incrementing = false;
	protected $primaryKey = 'customer';
	
	public $actions = ['changeproduct','register','ondemancategories'];
	public $conditional_action = [0 => 'ifUnregisteered', 1 => 'ifUnregisteered'];
	public $role_groups = [[],['company','distributor','dealer'],['supportagent','supportteam']];
	public $default_group = 0;
	public $group_actions = [0=>[],1=>[0,1],2=>[0,2]];
	public $modal_actions = [];
	
	protected function _GETROLEGROUP($rolename){ foreach($this->role_groups as $grp => $names) if(in_array($rolename,$names)) return $grp; return $this->default_group; }
	protected function _GETGROUPACTIONS($group){ return $this->group_actions[$group]; }
	protected function _GETROLEACTIONS($role){ return $this->_GETGROUPACTIONS($this->_GETROLEGROUP($role)); }
	public function _GETARRAYVALUES($array, $keys){ return array_map(function($key)use($array){ return $array[$key]; },$keys); }
	public function _GETAUTHUSER(){ return (Auth()->user())?:(Auth()->guard("api")->user()); }
	
	public function ifUnregisteered($Model){
		return is_null($Model->registered_on);
	}
	
	public $action_title = ['changeproduct' => 'Change product and edition','register' => 'Register customer','ondemancategories' => 'Allow/Disallow on demant support categories'];
	public $action_icon = ['changeproduct' => 'cd','register' => 'registration-mark','ondemancategories' => 'tasks'];
	
	protected $appends = ['available_actions'/*,'dealer','distributor'*/];
	public function getAvailableActionsAttribute($value = null){
		$role = $this->_GETAUTHUSER()->rolename;
		$role_actions = $this->_GETROLEACTIONS($role);
		if(!$this->exists) return $this->_GETARRAYVALUES($this->actions,$role_actions);
		$actions = array_filter($role_actions,function($ra){ return ($this->conditional_action && array_key_exists($ra,$this->conditional_action)) ? call_user_func([$this,$this->conditional_action[$ra]],$this) : true; });
		return $this->_GETARRAYVALUES($this->actions,$actions);
	}

	public function customer(){
		return $this->belongsTo("App\Models\Customer","customer","code");
	}
	
	public function login(){
		return $this->hasOne("App\Models\PartnerLogin","partner","customer");
	}
	
	public function product(){
		return $this->belongsTo("App\Models\Product","product","code")->withoutGlobalScope('own');
	}
	
	public function edition(){
		return $this->belongsTo("App\Models\Edition","edition","code")->withoutGlobalScope('own');
	}
	
	public function parent(){
		return $this->belongsToMany("App\Models\Partner","partner_relations","partner","parent");
	}
	
	public function extender(){
		return $this->belongsTo("App\Models\Partner","presale_extended_by","code");
	}
	
	public function supportteam(){
		return $this->hasMany("App\Models\CustomerSupportTeam","customer","customer")->whereStatus('ACTIVE');
	}

	public function parent1(){
		return $this->hasOne('App\Models\PartnerRelation','partner','customer');
	}
	
	public function scopeOfField($Q,$Field,$Value){
		return (is_array($Value)) ? $Q->whereIn($Field,$Value) : $Q->where($Field,$Value);
	}
	
	public function scopeOfType($Q,$Type = 'reg',$Field = null){
		if(is_null($Field)) return ($Type == 'reg') ? $this->scopeRegOnly($Q) : $this->scopeUnregOnly($Q);
		return ($Field == 'reg') ? $this->scopeRegOnly($Q) : $this->scopeUnregOnly($Q);
	}
	
	public function scopeRegOnly($Q){
		return $Q->whereNotNull('key')->whereNotNull('registered_on')->whereNotNull('serialno');
	}
	
	public function scopeUnregOnly($Q){
		return $Q->whereNull('key')->whereNull('registered_on')->whereNull('serialno');
	}
	
	public function scopeOfCustomer($Q,$Customer,$Bug = null){
		if(is_null($Bug)) return $this->scopeOfField($Q,'customer',$Customer);
		return $this->scopeOfField($Q,'customer',$Bug);
	}
	
	public function scopeofPeriod($Q,$null,$Period){
		$Period = date('Y-m-d H:i:s',$Period);
		return $Q->where('created_at', '>=', $Period);
	}
	
	public function scopeofTill($Q,$null,$Till){
		$Till = date('Y-m-d H:i:s',$Till);
		return $Q->where('created_at', '<', $Till);
	}
	
	public function scopeOwn($Q){
		return $Q->has('Customer');
	}

}

