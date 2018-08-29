<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechnicalSupportAgent extends Model
{
	
	protected static function boot(){
		parent::boot();
		static::addGlobalScope('only',function(\Illuminate\Database\Eloquent\Builder $builder){
			$builder->where(function($Q){ $Q->whereHas('Roles',function($Q){ $Q->where('rolename','supportagent'); }); });
		});
		static::addGlobalScope('active',function(\Illuminate\Database\Eloquent\Builder $builder){
			$builder->where(function($Q){ $Q->where('status','ACTIVE'); });
		});
		static::addGlobalScope('own',function(\Illuminate\Database\Eloquent\Builder $builder){
			$user = (\Auth()->user()) ?: Auth()->guard('api')->user();
			if($user){
				$me = $user->partner;
				if($user->rolename == "supportteam"){
					$builder->where(function($Q) use($me){ $Q->whereHas('Team',function($Q) use($me){ $Q->where('parent',$me); }); });
				} elseif($user->rolename == "supportagent"){
					$builder->where(function($Q) use($me){ $Q->where('code',$me); });
				}
			}
		});
	}
	
	protected function setCodeAttribute($Code = NULL){
		$this->attributes['code']	=	($Code)?:$this->NewCode();
	}
	
	public function NewCode(){
		$CodePrefixChar = "TSA"; $TotalCodeLength = 9;
		$LastNum = 0; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
		$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
		$LastCode = $this->withoutGlobalScope('own')->where($this->primaryKey,"REGEXP",$WhereValue)->max($this->primaryKey);
		if($LastCode) $LastNum = intval(mb_substr($LastCode,$PrefixLength));
		return ($CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT)));
	}

	protected $table = 'partners';
	protected $primaryKey = 'code';
	public $incrementing = false;
	public $timestamps = true;
	protected $fillable = ['code','name','created_by'];
	//protected $guarded = [];
	protected $hidden = ['status','status_description','created_by','created_at','updated_at'];
	//protected $visible = ['code','name','Details','Logins'];
	protected $with = ['Logins','Departments'];
		
	public function roles(){
		return $this->hasMany('App\Models\PartnerRole','partner','code');
	}

	public function details(){
		return $this->hasOne('App\Models\PartnerDetails','partner','code');
	}
	
	public function logins(){
		return $this->hasMany('App\Models\PartnerLogin','partner','code')->select('id','partner','email')->with(['Roles'	=>	function($Q){ $Q->select('login','role','rolename'); }]);
	}
	
	public function team(){
		return $this->hasOne('App\Models\PartnerRelation','partner','code')->with(['ParentDetails'	=>	function($Q){ $Q->select('code','name'); }]);
	}
	
	public function departments(){
		return $this->hasMany('App\Models\SupportAgentDepartment','agent','code')->with(['Department'	=>	function($Q){ $Q->select('code','name'); }]);
	}
	
	public function ticket_privilages(){
		return $this->hasOne('App\Models\AgentTicketPrivilage','agent','code');
	}
	
	public function distributor(){
		return $this->hasOne('App\Models\PartnerRelation','partner','code')->with('ParentDetails');
	}
	
	public function tasks(){
		return $this->belongsToMany('App\Models\TicketTask','task_responders','responder','task')->withoutGlobalScopes();
	}

}
