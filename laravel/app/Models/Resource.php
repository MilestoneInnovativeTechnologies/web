<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{

	public $incrementing = false;
	protected $primaryKey = 'code';
	protected $fillable = array('code', 'name', 'displayname', 'action', 'description', "created_by");
	public $timestamps = false;
	
	
	public function FillableFields(){
		return $this->fillable;
	}
	
	static public function ValidationRules(){
		return [
			"displayname"	=>	"required|unique:resources,displayname",
			"name"				=>	"required|unique:resources,name",
		];
	}
	
	static public function ValidationMessages(){
		return [
			"name.required"	=>	"Can't proceed with empty Base name.",
			"name.unique"	=>	"This Base name is already in use",
			"displayname.required"	=>	"Can't proceed with empty Display name.",
			"displayname.unique"	=>	"This Display name is already in use",
		];
	}
	
	public function NextCode(){
		$CodePrefixChar = "RES";
		$TotalCodeLength = 7;
		$LastNum = 0; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
		$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
		$LastCode = $this->where("code","REGEXP",$WhereValue)->orderBy("code","desc")->limit(1)->pluck("code");
		if(!empty($LastCode[0]))
			$LastNum = intval(mb_substr($LastCode[0],$PrefixLength));
		return $CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT));
	}
	
	public function roles(){
		return $this->belongsToMany('App\Models\Role','role_resource','resource','role')->withPivot('action')->whereStatus("ACTIVE");
	}
	
}
