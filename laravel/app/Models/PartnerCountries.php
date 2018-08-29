<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerCountries extends Model
{


	protected $table = 'partner_countries';
	protected $primaryKey = 'id';
	public $incrementing = true;
	public $timestamps = false;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['status','status_description','created_by','created_at','updated_at'];
	//protected $visible = ['code','name','Details','Logins'/*,'Roles'*/,'Privilage','Defaultst','Customers','Distributors'];
	protected $with = ['Partner','Country'];
	
	public function partner(){
		return $this->belongsTo('\App\Models\Partner','partner','code');
	}

	public function country(){
		return $this->belongsTo('\App\Models\Country','country','id');
	}






}
