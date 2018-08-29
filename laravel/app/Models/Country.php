<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
	public $timestamps = false;
	
	public function partners(){
		return $this->belongsToMany("App\Models\Partner","partner_countries","country","partner");
	}
	
	public function state(){
		return $this->hasMany("App\Models\State","country","id");
	}
}
