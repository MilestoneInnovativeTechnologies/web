<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
	public $timestamps = false;
	
	public function partners(){
		return $this->belongsToMany("App\Models\Partner","partner_details","state","partner");
	}
	
	public function country(){
		return $this->belongsTo("App\Models\Country","country","id");
	}
	
	public function city(){
		return $this->hasMany("App\Models\City","state","id");
	}

}
