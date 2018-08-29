<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerRemoteConnection extends Model
{
	
		protected static function boot()
		{
			parent::boot();
			static::addGlobalScope('own', function (\Illuminate\Database\Eloquent\Builder $builder) {
				$user = (\Auth()->user()) ?: Auth()->guard('api')->user(); if($user){
					$builder->where(function($Q){ $Q->has('Customer'); });
				}
			});
		}

	protected $table = 'customer_remote_connections';
	protected $primaryKey = 'id';
	public $incrementing = true;
	public $timestamps = true;
	protected $fillable = ['customer','appname','login','secret','remarks','created_by'];
	//protected $guarded = [];
	protected $hidden = ['created_at','updated_at'];
	//protected $visible = ['code','name','Details','Logins'];
	protected $with = ['Customer'];
	
	public function customer(){
		return $this->belongsTo('App\Models\Partner','customer','code');
	}
	
	public function createdby(){
		return $this->belongsTo('App\Models\Partner','created_by','code')->select('code','name');
	}

}
