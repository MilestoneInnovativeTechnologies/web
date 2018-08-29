<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributorCustomerContactMethod extends Model
{
	protected $table = 'distributor_customer_contact_methods';
	protected $primaryKey = 'id';
	public $incrementing = true;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['assigned_by','created_at','updated_at'];
	//protected $visible = ['code','name','Details','Logins','Roles'];
	//protected $with = [];
	
	
	
	public function _GETAUTHUSER(){ return (Auth()->user())?:(Auth()->guard("api")->user()); }
	
	
	public function add_new_common($distributor,$email,$sms){
		$this->whereDistributor($distributor)->whereNull('customer')->delete();
		$assigned_by = $this->_GETAUTHUSER()->partner;
		return $this->create(compact('distributor','email','sms','assigned_by'));
	}
	
	public function delete_all_exceptions($distributor){
		return $this->whereDistributor($distributor)->whereNotNull('customer')->delete();
	}
	
	public function add_exceptions($distributor,$CustomerObj){
		if(empty($CustomerObj)) return; $user = $this->_GETAUTHUSER()->partner;
		foreach($CustomerObj as $customer => $ES)
			$this->add_exception($distributor,$customer,$ES[0],$ES[1],$user);
	}
	
	public function add_exception($distributor,$customer,$email,$sms,$assigned_by){
		return $this->create(compact('distributor','customer','email','sms','assigned_by'));
	}
	
}
