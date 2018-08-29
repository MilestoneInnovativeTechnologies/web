<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
	public $timestamps = false;
	
	public function partners(){
		return $this->belongsToMany("App\Models\Partner","partner_details","city","partner");
	}
	
	public function state(){
		return $this->belongsTo("App\Models\State","state","id");
	}
}
