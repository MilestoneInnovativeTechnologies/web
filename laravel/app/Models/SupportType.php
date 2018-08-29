<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportType extends Model
{
	protected $table = 'support_types';
	protected $primaryKey = 'code';
	public $incrementing = false;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	//protected $hidden = [];
	protected $visible = ['code','name'];

	protected function setCodeAttribute($Code = NULL){
		$this->attributes['code']	=	($Code)?:$this->NewCode();
	}
	
	public function NewCode(){
		$CodePrefixChar = "ST"; $TotalCodeLength = 6;
		$LastNum = 0; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
		$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
		$LastCode = $this->where($this->primaryKey,"REGEXP",$WhereValue)->max($this->primaryKey);//orderBy($this->primaryKey,"desc")->limit(1)->pluck($this->primaryKey);
		if($LastCode) $LastNum = intval(mb_substr($LastCode,$PrefixLength));
		return ($CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT)));
	}
	
	public function ValidationRules(){
		return [
			"code"	=>	"required|unique:support_types,code",
			"name"	=>	"required|unique:support_types,name"
		];
	}
	
	public function ValidationMessages(){
		return [
			"code.required"	=>	"Support Type code is Mandatory",
			"code.unique"	=>	"Support Type Code is already taken, Choose a unique one",
			"name.required"	=>	"Support Type Name is Mandatory",
			"name.unique"	=>	"Support Type Name is already taken, Choose a unique one"
		];
	}


}
