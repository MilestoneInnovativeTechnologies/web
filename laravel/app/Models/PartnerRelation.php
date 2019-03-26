<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerRelation extends Model
{
	public $timestamps = false;
	public $incrementing = false;
	protected $primaryKey = 'partner';
	protected $fillable = array('partner', 'parent');
	
	public function parentDetails(){
		return $this->belongsTo('App\Models\Partner','parent','code')->withoutGlobalScope('active');
	}
	
	public function childDetails(){
		return $this->belongsTo('App\Models\Partner','partner','code')->withoutGlobalScope('active');
	}
	
	public function roles(){
		return $this->hasMany('App\Models\PartnerRole','partner','parent');
	}
	
	public function partnerroles(){
		return $this->hasMany('App\Models\PartnerRole','partner','partner');
	}
	
	public function parent1(){
		return $this->hasOne('App\Models\PartnerRelation','partner','parent');
	}
	
	public function children(){
		return $this->hasMany('App\Models\PartnerRelation','parent','partner');
	}
	
	public function registration(){
		return $this->hasMany('App\Models\CustomerRegistration','customer','partner')->select('customer','seqno','product','edition','registered_on','serialno','key','created_at')->with(['Product' => function($Q){ $Q->select('code','name'); }, 'Edition' => function($Q){ $Q->select('code','name'); }, 'Customer' => function($Q){ $Q->select('code','name'); }]);
	}
	
	public function parentCountries(){
		return $this->hasMany('App\Models\PartnerCountries','partner','parent');
	}
	
}
