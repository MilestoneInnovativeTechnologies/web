<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerRole extends Model
{

	protected $fillable = array('login', 'partner', 'role', 'status', 'created_by');
	//protected $guarded  = ["created_at"];
	
	public function details(){
		return $this->belongsTo('App\Models\Role','role','code');
	}
	
	public function partnerDetails(){
		return $this->partner();
	}
	
	public function partner(){
		return $this->belongsTo('App\Models\Partner','partner','code');
	}
	
	public function login(){
		return $this->belongsTo('App\Models\PartnerLogin','login','id');
	}
	
	public function email(){
		return $this->login();
	}
	
	public function scopeRole($Q,$RoleName){
		$Q->where('rolename',$RoleName);
	}
	
	public function scopeDealer($Q){
		return $this->scopeRole($Q,'dealer');
	}
	
	public function scopeDistributor($Q){
		return $this->scopeRole($Q,'distributor');
	}
	
	public function scopeCustomer($Q){
		return $this->scopeRole($Q,'customer');
	}

}
