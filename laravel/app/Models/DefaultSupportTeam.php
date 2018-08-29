<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefaultSupportTeam extends Model
{


	protected $table = 'default_support_teams';
	protected $primaryKey = 'id';
	public $incrementing = true;
	public $timestamps = false;
	//protected $fillable = ['code'];
	protected $guarded = [];
	//protected $hidden = [];
	protected $visible = ['supportteam','Partner'];
	protected $with = ['Partner'];
	
	public function partner(){
		return $this->belongsTo('App\Models\Partner','supportteam','code');
	}
	protected static function boot(){
		parent::boot();
		static::addGlobalScope('only',function(\Illuminate\Database\Eloquent\Builder $builder){
			$builder->whereId('1');
		});
	}
}
