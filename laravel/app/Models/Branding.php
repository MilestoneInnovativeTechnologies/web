<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branding extends Model
{
	protected $table = 'brandings';
	protected $primaryKey = 'id';
	public $incrementing = true;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['created_at','updated_at'];
	//protected $visible = [];
	protected $with = ['Links','Products'];
	
	
	public function links(){
		return $this->hasMany('App\Models\BrandingLinks','brand','id');
	}
	
	public function products(){
		return $this->hasMany('App\Models\BrandingProducts','brand','id');
	}
	
	public function distributor(){
		return $this->belongsToMany('App\Models\Partner','distributor_brandings','branding','distributor');
	}
	
	public function main(){
		return $this->hasMany('App\Models\DistributorBranding','branding','id');
	}
	
}
