<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTeamDistributors extends Model
{
	
	protected static function boot(){
		parent::boot();
		static::addGlobalScope('OwnDistributors',function(\Illuminate\Database\Eloquent\Builder $builder){
			$user = (\Auth()->user()) ?: Auth()->guard('api')->user();
			$TST = ($user->Partner->Roles->contains('name','supportteam')) ? $user->Partner : $user->Partner->ParentDetails->first();
			$Distributors = \App\Models\DistributorSupportTeam::whereSupportteam($TST->code)->pluck('distributor')->toArray();
			$builder->whereIn('code',$Distributors);
		});
	}


	protected $table = 'partners';
	protected $primaryKey = 'code';
	public $incrementing = false;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['status','status_description','created_by','created_at','updated_at'];
	//protected $visible = ['code','name','Details','Logins'];
	protected $with = ['Details.City.State.Country','Logins'];

	public function details(){
		return $this->hasOne('App\Models\PartnerDetails','partner','code');
	}
	
	public function logins(){
		return $this->hasMany('App\Models\PartnerLogin','partner','code')->select('id','partner','email');
	}
	
	public function parent1(){
		return $this->hasMany('App\Models\PartnerRelation','partner','code')->with(['ParentDetails'	=>	function($Q){ $Q->select('code','name'); }]);
	}

}
