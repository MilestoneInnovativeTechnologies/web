<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandingProducts extends Model
{
	protected $table = 'branding_products';
	protected $primaryKey = 'id';
	public $incrementing = true;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['created_at','updated_at'];
	//protected $visible = [];
	protected $with = ['Product','Edition'];
	
	public function product(){
		return $this->belongsTo('App\Models\Product','product','code')->select('code','name','description_public')->with(['Editions' => function($Q){ $Q->select('code','name'); }]);
	}
	
	public function edition(){
		return $this->belongsTo('App\Models\Edition','edition','code')->select('code','name');
	}
	
}
