<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerIndustry extends Model
{
  protected $table = 'customer_industry';
	public $incrementing = false;
	protected $primaryKey = 'code';
	protected $fillable = array('code', 'name', "created_by");
	public $timestamps = false;
	
	public function partners(){
		return $this->belongsToMany("App\Models\Partner","partner_details","industry","partner");
	}
	
	public function NextCode(){
		$CodePrefixChar = "CI";
		$TotalCodeLength = 6;
		$LastNum = 0; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
		$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
		$LastCode = $this->where("code","REGEXP",$WhereValue)->orderBy("code","desc")->limit(1)->pluck("code");
		if(!empty($LastCode[0]))
			$LastNum = intval(mb_substr($LastCode[0],$PrefixLength));
		return $CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT));
	}

}
