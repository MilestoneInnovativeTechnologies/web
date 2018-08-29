<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributorBranding extends Model
{
	protected $table = 'distributor_brandings';
	protected $primaryKey = 'id';
	public $incrementing = true;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['created_at','updated_at'];
	//protected $visible = [];
	protected $with = ['Distributor'];
	
	public function distributor(){
		return $this->belongsTo('App\Models\Partner','distributor','code')->select('code','name');
	}
	
	public function branding(){
		return $this->belongsTo('App\Models\Branding','branding','id');
	}
	
}
