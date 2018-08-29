<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketStatus extends Model
{


	protected $table = 'ticket_statuses';
	protected $primaryKey = 'code';
	public $incrementing = false;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	//protected $hidden = [];
	protected $visible = ['code','name','After'];
	protected $with = ["After"];

	protected function setCodeAttribute($Code = NULL){
		$this->attributes['code']	=	($Code)?:$this->NewCode();
	}
	
	public function NewCode(){
		$CodePrefixChar = "TS"; $TotalCodeLength = 6;
		$LastNum = 0; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
		$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
		$LastCode = $this->where($this->primaryKey,"REGEXP",$WhereValue)->max($this->primaryKey);//orderBy($this->primaryKey,"desc")->limit(1)->pluck($this->primaryKey);
		if($LastCode) $LastNum = intval(mb_substr($LastCode,$PrefixLength));
		return ($CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT)));
	}
	
	public function ValidationRules(){
		return [
			"code"	=>	"required|unique:ticket_statuses,code",
			"name"	=>	"required|unique:ticket_statuses,name",
			"after"	=>	"nullable|exists:ticket_statuses,code"
		];
	}
	
	public function ValidationMessages(){
		return [
			"code.required"	=>	"Ticket Status code is Mandatory",
			"code.unique"	=>	"Ticket Status Code is already taken, Choose a unique one",
			"name.required"	=>	"Ticket Status Name is Mandatory",
			"name.unique"	=>	"Ticket Status Name is already taken, Choose a unique one",
			"after.exists"	=>	"The selected 'After Status' field is not valid"
		];
	}
	
	public function after(){
		return $this->belongsTo('App\Models\TicketStatus','after','code');
	}
	
	public function similiar(){
		return $this->belongsTo('App\Models\TicketStatus','similiar_to_status','code');
	}


}
