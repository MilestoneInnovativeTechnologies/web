<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeatureValueOption extends Model
{
		protected $fillable = array("option","order");
		
    public function feature(){
			return $this->belongsTo('App\Models\Feature','feature','id');
		}
}
