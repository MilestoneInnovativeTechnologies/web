<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketDetailType extends Model
{
	protected $table = 'ticket_detail_types';
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
		$CodePrefixChar = "TDT"; $TotalCodeLength = 7;
		$LastNum = 0; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
		$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
		$LastCode = $this->where($this->primaryKey,"REGEXP",$WhereValue)->max($this->primaryKey);//orderBy($this->primaryKey,"desc")->limit(1)->pluck($this->primaryKey);
		if($LastCode) $LastNum = intval(mb_substr($LastCode,$PrefixLength));
		return ($CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT)));
	}
	
	public function ValidationRules(){
		return [
			"code"	=>	"required|unique:ticket_detail_types,code",
			"name"	=>	"required|unique:ticket_detail_types,name"
		];
	}
	
	public function ValidationMessages(){
		return [
			"code.required"	=>	"Ticket Detail Type code is Mandatory",
			"code.unique"	=>	"Ticket Detail Type Code is already taken, Choose a unique one",
			"name.required"	=>	"Ticket Detail Type Name is Mandatory",
			"name.unique"	=>	"Ticket Detail Type Name is already taken, Choose a unique one"
		];
	}


}
