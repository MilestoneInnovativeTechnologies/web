<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
  
	public $incrementing = false;
	protected $primaryKey = 'code';
	protected $fillable = ['code', 'date', 'distributor', 'description', 'price', 'currency', 'exchange_rate', 'amount', 'type', 'status', 'user', 'identifier'];
	protected $table = "transactions";
	
	
	public function distributor(){
		return $this->belongsTo("App\Models\Partner","distributor","code");
	}
	
	public function user(){
		return $this->belongsTo("App\Models\Partner","user","code");
	}

	public function setCodeAttribute($chr){
		$CodePrefixChar = "TXN" . $chr; $TotalCodeLength = 12;
		$LastNum = 0; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
		$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
		$LastCode = $this->where("code","REGEXP",$WhereValue)->orderBy("code","desc")->limit(1)->pluck("code");
		if(!empty($LastCode[0])) $LastNum = intval(mb_substr($LastCode[0],$PrefixLength));
		$this->attributes["code"]	=	$CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT));
	}	
	
}