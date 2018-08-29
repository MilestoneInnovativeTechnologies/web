<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportAgentDepartment extends Model
{
	
	protected static function boot(){
		parent::boot();
		static::addGlobalScope('active',function(\Illuminate\Database\Eloquent\Builder $builder){
			$builder->where(function($Q){ $Q->where('status','ACTIVE'); });
		});
		static::addGlobalScope('own',function(\Illuminate\Database\Eloquent\Builder $builder){
			$user = (\Auth()->user()) ?: Auth()->guard('api')->user();
			if($user->Role->contains('rolename','supportagent')){
				$builder->whereAgent($user->partner);
			} elseif($user->Role->contains('rolename','supportteam')) {
				$builder->whereIn('agent',\App\Models\PartnerRelation::whereParent($user->partner)->pluck('partner')->toArray());
			}
		});
	}

	protected $table = 'support_agent_departments';
	protected $primaryKey = 'id';
	public $incrementing = true;
	public $timestamps = true;
	protected $fillable = ['agent','department','assigned_by'];
	//protected $guarded = [];
	protected $hidden = ['created_at','updated_at'];
	//protected $visible = ['code','name','Details','Logins'];
	protected $with = ['Agent','Department'];

	public function agent(){
		return $this->belongsTo('App\Models\Partner','agent','code')->select('code','name');
	}
	
	public function department(){
		return $this->belongsTo('App\Models\SupportDepartment','department','code')->select('code','name');
	}

}
