<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
	public $incrementing = false;
	protected $primaryKey = 'code';
	protected $fillable = array('code', 'name', 'description', "created_by", 'status');
	protected $with = ['Details'];
	
	public function NextCode(){
		$CodePrefixChar = "PL";
		$TotalCodeLength = 6;
		$LastNum = 0; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
		$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
		$LastCode = $this->where("code","REGEXP",$WhereValue)->orderBy("code","desc")->limit(1)->pluck("code");
		if(!empty($LastCode[0])) $LastNum = intval(mb_substr($LastCode[0],$PrefixLength));
		return $CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT));
	}
	
	public function details(){
		return $this->hasMany("App\Models\PriceListDetails","pricelist","code");
	}
	
}
