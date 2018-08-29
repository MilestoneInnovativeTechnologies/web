<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
		public $incrementing = false;
		protected $primaryKey = 'code';
		protected $fillable = array('code', 'name', 'base_name', 'type', 'description_public', 'description_internal', "created_by");
		
		public function NextCode(){
			$CodePrefixChar = "PKG";
			$TotalCodeLength = 6;
			$LastNum = 0; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
			$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
			$LastCode = $this->where("code","REGEXP",$WhereValue)->orderBy("code","desc")->limit(1)->pluck("code");
			if(!empty($LastCode[0]))
				$LastNum = intval(mb_substr($LastCode[0],$PrefixLength));
			return $CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT));
		}
		
		public function GetMyFillableFields(){
			return $this->fillable;
		}
		
		public function MyValidationRules(){
			return array(
				"name"	=>	"required|unique:packages,name",
				"base_name"	=>	"required|unique:packages,base_name",
				"type"	=>	"required|in:Onetime,Update",
			);
		}
}
