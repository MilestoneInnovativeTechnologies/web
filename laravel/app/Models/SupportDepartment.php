<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportDepartment extends Model
{
	protected $table = 'support_departments';
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
		$CodePrefixChar = "SDPT"; $TotalCodeLength = 8;
		$LastNum = 0; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
		$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
		$LastCode = $this->where($this->primaryKey,"REGEXP",$WhereValue)->max($this->primaryKey);//orderBy($this->primaryKey,"desc")->limit(1)->pluck($this->primaryKey);
		if($LastCode) $LastNum = intval(mb_substr($LastCode,$PrefixLength));
		return ($CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT)));
	}
	
	public function ValidationRules(){
		return [
			"code"	=>	"required|unique:support_departments,code",
			"name"	=>	"required|unique:support_departments,name"
		];
	}
	
	public function ValidationMessages(){
		return [
			"code.required"	=>	"Department code is Mandatory",
			"code.unique"	=>	"Department Code is already taken, Choose a unique one",
			"name.required"	=>	"Department Name is Mandatory",
			"name.unique"	=>	"Department Name is already taken, Choose a unique one"
		];
	}


}
