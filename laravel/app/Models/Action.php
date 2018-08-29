<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{

	//public $incrementing = false;
	//public $timestamps = false;
	protected $fillable = array('name', 'displayname', 'description', "created_by");
	
	public function FillableFields(){
		return $this->fillable;
	}

	static public function ValidationRules(){
		return [
			"displayname"	=>	"required|unique:actions,displayname",
			"name"				=>	"required|unique:actions,name",
		];
	}
	
	static public function ValidationMessages(){
		return [
			"name.required"	=>	"Can't proceed with empty Base name.",
			"name.unique"	=>	"This Base Name is already in use",
			"displayname.required"	=>	"Can't proceed with empty Display name.",
			"displayname.unique"	=>	"This Display name is already in use",
		];
	}

}
