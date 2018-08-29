<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerDetails extends Model
{

	public $incrementing = false;
	protected $primaryKey = 'code';
	//protected $fillable = array('code', 'displayname', 'name', 'description', 'status');
	protected $guarded  = ["created_at"];
	protected $with = ['City.State.Country'];
	
	public function industry(){
		return $this->belongsTo('App\Models\CustomerIndustry','industry','code');
	}
	
	public function city(){
		return $this->belongsTo('App\Models\City','city','id');
	}
	
	public function state(){
		return $this->belongsTo('App\Models\State','state','id');
	}
	
	public function NextCode(){
		$CodePrefixChar = "PDTS";
		$TotalCodeLength = 12;
		$LastNum = 0; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
		$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
		$LastCode = $this->where("code","REGEXP",$WhereValue)->orderBy("code","desc")->limit(1)->pluck("code");
		if(!empty($LastCode[0]))
			$LastNum = intval(mb_substr($LastCode[0],$PrefixLength));
		return $CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT));
	}

	
	protected function setCodeAttribute($Code = NULL){
		$this->attributes['code']	=	($Code)?:$this->NextCode();
	}
	
	public function pricelist(){
		return $this->belongsTo('App\Models\PriceList','pricelist','code');
	}
}
