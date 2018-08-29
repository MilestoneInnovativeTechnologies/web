<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dealer extends Model
{
	protected static function boot(){
		parent::boot();
		static::addGlobalScope('only',function(\Illuminate\Database\Eloquent\Builder $builder){
			$builder->where(function($Q){ $Q->whereHas('Roles',function($Q){ $Q->where('rolename','dealer'); }); });
		});
		static::addGlobalScope('latest',function(\Illuminate\Database\Eloquent\Builder $builder){
			$builder->latest();
		});
		static::addGlobalScope('own',function(\Illuminate\Database\Eloquent\Builder $builder){
			$user = (\Auth()->user()) ?: Auth()->guard('api')->user(); if($user){
				$me = $user->partner;
				if($user->rolename == 'distributor'){
					$builder->where(function($Q) use($me){ $Q->whereHas('Parent1',function($Q) use($me){ $Q->where('parent',$me); }); });
				} elseif($user->rolename == 'dealer'){
					$builder->where(function($Q) use($me){ $Q->where('code',$me); });
				} elseif($user->rolename == 'customer'){
					$ParentRoles = $user->Parent->Roles;
					if($ParentRoles->contains('rolename','dealer')) $dealer = $user->Parent->parent;
					else $dealer = null;
					$builder->where(function($Q) use($dealer){ $Q->where('code',$dealer); });
				} elseif($user->rolename == 'supportteam'){
					$builder->where(function($Q) use($me){ $Q->whereHas('Distributor',function($Q) use($me){ $Q->whereHas('Supportteam',function($Q) use($me){ $Q->where('supportteam',$me); }); }); });
				} elseif($user->rolename == 'supportagent'){
					$myTeam = $user->Parent->parent;
					$builder->where(function($Q) use($myTeam){ $Q->whereHas('Distributor',function($Q) use($myTeam){ $Q->whereHas('Supportteam',function($Q) use($myTeam){ $Q->where('supportteam',$myTeam); }); }); });
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
	
	public function parent1(){
		return $this->hasOne('App\Models\PartnerRelation','partner','code');
	}

	public function customers(){
		return $this->childs()->with('ChildDetails','Registration');
	}

	public function scopeWithCustomers($Q){
		$Q->with(['Customers.Croles'	=>	function($Q){
			$Q->where(['rolename'=>'customer','status'=>'ACTIVE']);
		}]);
	}
		
	public function countries(){
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

	public function tickets(){
		return $this->hasMany("App\Models\Ticket","created_by","code");
	}
		
	public function distributor(){
		return $this->belongsToMany("App\Models\Distributor","partner_relations","partner","parent");
	}
	
}
