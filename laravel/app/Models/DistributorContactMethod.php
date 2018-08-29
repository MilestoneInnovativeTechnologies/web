<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributorContactMethod extends Model
{
	protected $table = 'distributor_contact_methods';
	protected $primaryKey = 'id';
	public $incrementing = true;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['assigned_by','created_at','updated_at'];
	//protected $visible = ['code','name','Details','Logins','Roles'];
	//protected $with = [];
	
	
	
	public function _GETAUTHUSER(){ return (Auth()->user())?:(Auth()->guard("api")->user()); }
	
	
	
	public function add_new($distributor,$email,$sms){
		$this->whereDistributor($distributor)->delete();
		$assigned_by = $this->_GETAUTHUSER()->partner;
		return $this->create(compact('distributor','email','sms','assigned_by'));
	}
	
	
	
	
}
