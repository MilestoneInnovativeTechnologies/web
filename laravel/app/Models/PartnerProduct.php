<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerProduct extends Model
{
    protected $fillable = [
			"partner", "product", "edition", "created_by"
		];
	
	
	public function products(){
		return $this->belongsTo("App\Models\Product","product","code")->whereActive("1");
	}
	
	public function editions(){
		return $this->belongsTo("App\Models\Edition","edition","code")->whereActive("1");
	}
	
	public function product(){
		return $this->products();
	}
	
	public function edition(){
		return $this->editions();
	}
	
	public function partner(){
		return $this->belongsTo("App\Models\Partner","partner","code")->whereStatus("ACTIVE");
	}
	
	public function login(){
		return $this->hasOne("App\Models\PartnerLogin","partner","partner");
	}
}
