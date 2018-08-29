<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CustomerSupportTeam extends Model
{

	
	protected static function boot(){
		parent::boot();
		static::addGlobalScope('status',function(Builder $builder){
			$builder->where(function($Q){ $Q->whereStatus('ACTIVE'); });
		});
	}

	protected $table = 'customer_support_teams';
	protected $primaryKey = 'id';
	public $incrementing = true;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['id','assigned_by','status','created_at','updated_at'];
	//protected $visible = ['customer','supportteam','product','edition','Partner','Team','Product','Edition'];
	protected $with = ['Partner','Team','Product','Edition'];
	
	public function partner(){
		return $this->belongsTo('App\Models\Partner','customer','code');
	}
	
	public function team(){
		return $this->belongsTo('App\Models\Partner','supportteam','code');
	}
	
	public function product(){
		return $this->belongsTo('App\Models\Product','product','code')->withoutGlobalScopes();
	}
	
	public function edition(){
		return $this->belongsTo('App\Models\Edition','edition','code')->withoutGlobalScopes();
	}
	
	public function parent1(){
		return $this->hasOne('App\Models\PartnerRelation','partner','customer');
	}
	
	public function dealer(){
		return $this->parent1();
	}
	
	public function distributor(){
		return $this->parent1();
	}
	
	public function scopeWithDealer($Q){
		$Q->with(['Dealer'	=>	function($Q){
			$Q->with(['Roles'	=>	function($Q){ $Q->whereRolename('dealer'); },'Parent1.Roles'	=>	function($Q){ $Q->whereRolename('distributor'); }]);
		}]);
	}
	
	public function scopeWithDistributor($Q){
		$Q->with(['Distributor.Roles'	=>	function($Q){
			$Q->whereRolename('distributor');
		}]);
	}
	
}
