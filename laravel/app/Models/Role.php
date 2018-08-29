<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

	public $incrementing = false;
	protected $primaryKey = 'code';
	protected $fillable = array('code', 'displayname', 'name', 'description', 'status', "created_by");
	
	public function FillableFields(){
		return $this->fillable;
	}
	
	static public function ValidationRules(){
		return [
			"displayname"	=>	"required|unique:roles,displayname",
			"name"				=>	"required|unique:roles,name",
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
		$CodePrefixChar = "ROL";
		$TotalCodeLength = 6;
		$LastNum = 0; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
		$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
		$LastCode = $this->where("code","REGEXP",$WhereValue)->orderBy("code","desc")->limit(1)->pluck("code");
		if(!empty($LastCode[0]))
			$LastNum = intval(mb_substr($LastCode[0],$PrefixLength));
		return $CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT));
	}
	
	public function partners(){
		return $this->belongsToMany('App\Models\Partner','partner_roles','role','partner')->wherePivot("status","ACTIVE");
	}
	
	public function resources(){
		return $this->belongsToMany('App\Models\Resource','role_resource','role','resource')->withPivot('action')->whereStatus("ACTIVE");
	}
	
	public function login(){
		return $this->hasMany('App\Models\PartnerLogin','partner','code')->whereStatus("ACTIVE");
	}

}
