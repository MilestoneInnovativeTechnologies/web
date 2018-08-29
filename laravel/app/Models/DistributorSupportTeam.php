<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributorSupportTeam extends Model
{
	
	protected static function boot(){
		parent::boot();
		static::addGlobalScope('status',function(\Illuminate\Database\Eloquent\Builder $builder){
			$builder->where(function($Q){ $Q->where('status','ACTIVE'); });
		});
		static::addGlobalScope('own',function(\Illuminate\Database\Eloquent\Builder $builder){
			$user = (\Auth()->user()) ?: Auth()->guard('api')->user();
			if($user){
				$me = $user->partner;
				if($user->rolename == 'distributor'){
					$builder->where(function($Q) use($me){ $Q->where('distributor',$me); });
				} elseif($user->rolename == 'supportteam'){
					$builder->where(function($Q) use($me){ $Q->where('supportteam',$me); });
				} elseif($user->rolename == 'supportagent'){
					$myTeam = $user->Parent->parent;
					$builder->where(function($Q) use($myTeam){ $Q->where('supportteam',$myTeam); });
				} else {
					$builder->where(function($Q){ $Q->has('Distributor'); });
				}
			}
		});
	}

	protected $table = 'distributor_supportteam';
	protected $primaryKey = 'id';
	public $incrementing = true;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	//protected $hidden = [];
	protected $visible = ['distributor','supportteam','Partner','Team'];
	protected $with = ['Partner','Team'];
	
	public function partner(){
		return $this->distributor();
	}
	
	public function distributor(){
		return $this->belongsTo('App\Models\Distributor','distributor','code');
	}
	
	public function team(){
		return $this->belongsTo('App\Models\Partner','supportteam','code');
	}
	
}
