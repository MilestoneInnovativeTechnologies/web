<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
		protected $fillable = array('name', 'category', 'value_type', 'description_public', 'description_internal', "created_by");

		public function GetMyFillableFields(){
			return $this->fillable;
		}
		
		public function MyValidationRules(){
			return array(
				"name"	=>	"required|unique:editions,name",
				"value_type"	=>	"required|in:YES/NO,STRING,OPTION,MULTISELECT",
			);
		}
		
		public function options(){
			return $this->hasMany('App\Models\FeatureValueOption','feature','id');
		}
		
}
