<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTeam extends Model
{

		/**
		 * The "booting" method of the model.
		 *
		 * @return void
		 */
		protected static function boot()
		{
			parent::boot();
			static::addGlobalScope('only',function(\Illuminate\Database\Eloquent\Builder $builder){
				$builder->where(function($Q){ $Q->whereHas('Roles',function($Q){ $Q->where('rolename','supportteam'); }); });
			});
			static::addGlobalScope('SupportTeam', function (\Illuminate\Database\Eloquent\Builder $builder) {
				$user = (\Auth()->user()) ?: Auth()->guard('api')->user();
				if($user){
					$me = $user->partner;
					if($user->rolename == 'supportagent'){
						$myTeam = $user->Parent->parent;
						$builder->where(function($Q) use($myTeam){ $Q->where('code',$myTeam); });
					} elseif($user->rolename == 'supportteam'){
						$builder->where(function($Q) use($me){ $Q->where('code',$me); });
					} elseif($user->rolename == 'distributor'){
						$builder->where(function($Q){ $Q->has('Distributors1'); });
					} elseif($user->rolename == 'dealer'){
						$builder->where(function($Q){ $Q->has('Distributors1'); });
					} elseif($user->rolename == 'customer'){
						$builder->where(function($Q){ $Q->has('Customers1'); });
					}
				}
			});
		}


	protected $table = 'partners';
	protected $primaryKey = 'code';
	public $incrementing = false;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['status','status_description','created_by','created_at','updated_at'];
	//protected $visible = ['code','name','Details','Logins'/*,'Roles'*/,'Privilage','Defaultst','Customers','Distributors'];
	protected $with = ['Details','Logins','Roles','Privilage','Defaultst'];
	
	
	protected function setCodeAttribute($Code = NULL){
		$this->attributes['code']	=	($Code)?:$this->NewCode();
	}
	
	public function NewCode(){
		$CodePrefixChar = "TST"; $TotalCodeLength = 9;
		$LastNum = 0; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
		$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
		$LastCode = $this->where($this->primaryKey,"REGEXP",$WhereValue)->max($this->primaryKey);//orderBy($this->primaryKey,"desc")->limit(1)->pluck($this->primaryKey);
		if($LastCode) $LastNum = intval(mb_substr($LastCode,$PrefixLength));
		return ($CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT)));
	}
	
	public function details(){
		return $this->hasOne('App\Models\PartnerDetails','partner','code');
	}
	
	public function logins(){
		return $this->hasMany('App\Models\PartnerLogin','partner','code');
	}
	
	public function roles(){
		return $this->hasMany('App\Models\PartnerRole','partner','code');
	}
	
	public function privilage(){
		return $this->hasOne('App\Models\SupportTeamPrivilage','support_team','code');
	}
	
	public function defaultst(){
		return $this->hasOne('App\Models\DefaultSupportTeam','supportteam','code');
	}
	
	public function customers(){
		return $this->hasMany('App\Models\CustomerSupportTeam','supportteam','code');
	}
	
	public function customers1(){
		return $this->belongsToMany('App\Models\Customer','customer_support_teams','supportteam','customer')->wherePivot('status','ACTIVE')->withoutGlobalScope('latest');
	}
	
	public function distributors(){
		return $this->hasMany('App\Models\DistributorSupportTeam','supportteam','code');
	}
	
	public function distributors1(){
		return $this->belongsToMany('App\Models\Distributor','distributor_supportteam','supportteam','distributor')->wherePivot('status','ACTIVE')->withoutGlobalScope('latest');
	}
	
	public function countries(){
		return $this->hasMany('App\Models\PartnerCountries','partner','code');
	}
	
	static function ValidationRules(){
		return [
			"code"				=>	"required|unique:partners,code",
			"name"				=>	"required|unique:partners,name",
			"country"			=>	"required|exists:countries,id",
			"email"				=>	"required|email|unique:partner_logins,email",
			"phone"				=>	"required",
		];
	}
	
	static function ValidationMessages(){
		return [
			"code.required"					=>	"Team code is required field.",
			"code.unique"						=>	"Code you entered is already taken, please choose a unique one.",
			"name.required"					=>	"Team name is required.",
			"name.unique"						=>	"Name entered is already takem, Please choose another one.",
			"country.required"			=>	"Please select country.",
			"country.exists"				=>	"The country selected doesn't exists in records, Please select from the list.",
			"email.required"				=>	"Email is a required field.",
			"email.email"						=>	"Email doesn't seems to be a valid one, Please correct.",
			"email.unique"					=>	"Email has already registered, Please provide another one.",
			"phone.required"				=>	"Phone number is required",
		];
	}
	
	public function tickets(){
		return $this->belongsToMany('App\Models\Ticket','ticket_support_teams','team','ticket');
	}
	
	public function agents(){
		return $this->hasManyThrough('App\Models\TechnicalSupportAgent','App\Models\PartnerRelation','parent','code','code');
	}
}
