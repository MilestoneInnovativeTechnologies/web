<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTeamCustomer extends Model
{
	
	protected static function boot(){
		parent::boot();
		static::addGlobalScope('OwnCustomers',function(\Illuminate\Database\Eloquent\Builder $builder){
			$user = (\Auth()->user()) ?: Auth()->guard('api')->user();
			$TST = ($user->Partner->Roles->contains('name','supportteam')) ? $user->Partner : $user->Partner->ParentDetails->first();
			$Customers = \App\Models\CustomerSupportTeam::whereSupportteam($TST->code)->pluck('customer')->toArray();
			$builder->whereIn('code',$Customers);
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
	
	public function product(){
		return $this->belongsToMany('App\Models\Product','customer_registrations','customer','product')->select('code','name')->withPivot('seqno','edition','remarks')->with(['Editions'	=>	function($Q){ $Q->select('code','name')->withPivot('level','description'); }]);
	}

}
