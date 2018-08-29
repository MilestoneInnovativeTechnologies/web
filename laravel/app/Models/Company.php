<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('only', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$builder->whereCode('COMPANY')->with('Details.City.State.Country','Logins');
		});
	}
	protected $table = 'partners';
	protected $primaryKey = 'code';
	public $incrementing = false;
	public $timestamps = true;
	protected $hidden = ['status','status_description','created_by','created_at','updated_at'];
	//protected $with = ['Details.City.State.Country','Logins'];
	protected $appends = ['address','phone','email','emails'];
	public function getAddressAttribute(){
		$AdrArray = []; $Details = $this->Details;
		$AdrArray[] = $Details->address1; $AdrArray[] = $Details->address2;
		$AdrArray[] = $Details->City->name; $AdrArray[] = $Details->City->State->name;
		$AdrArray[] = $Details->City->State->Country->name;
		return $AdrArray;
	}
	public function getPhoneAttribute(){
		$Details = $this->Details;
		return '+'.($Details->phonecode).'-'.($Details->phone);
	}
	public function getEmailAttribute(){
		return $this->Logins[0]->email;
	}
	public function getEmailsAttribute(){
		return $this->Logins->pluck('email')->toArray();
	}
	public function details(){
		return $this->hasOne('App\Models\PartnerDetails','partner','code');
	}
	public function logins(){
		return $this->hasMany('App\Models\PartnerLogin','partner','code')->whereStatus('ACTIVE');
	}		
	public function roles(){
		return $this->hasMany('App\Models\PartnerRole','partner','code');
	}
}
