<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Partner extends Model
{
	
	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('active', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$builder->where(function($Q){ $Q->where('status','!=','INACTIVE'); });
		});
	}

	public $incrementing = false;
	protected $primaryKey = 'code';
	protected $fillable = array('code', 'name', 'status', 'status_description', "created_by");
	
	public function FillableFields(){
		return $this->fillable;
	}

	public function CustomerNextCode(){
		return $this->NextCode("CUST");
	}

	public function DistributorNextCode(){
		return $this->NextCode("DIST");
	}

	public function DealerNextCode(){
		return $this->NextCode("DELR");
	}

	public function NextCode($CodePrefixChar){
		$TotalCodeLength = 10;
		$LastNum = 0; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
		$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
		$LastCode = $this->where("code","REGEXP",$WhereValue)->orderBy("code","desc")->limit(1)->pluck("code");
		if(!empty($LastCode[0]))
			$LastNum = intval(mb_substr($LastCode[0],$PrefixLength));
		return $CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT));
	}	
		
	public function details(){
		return $this->hasOne('App\Models\PartnerDetails','partner','code');
	}
	
	public function logins(){
		return $this->hasMany('App\Models\PartnerLogin','partner','code')->whereStatus('ACTIVE');
	}
	
	public function currentlogin(){
		return $this->hasOne('App\Models\PartnerLogin','partner','code')->whereStatus('ACTIVE')->whereEmail(Auth()->user()->email);
	}
	
	public function roles(){
		return $this->belongsToMany('App\Models\Role','partner_roles','partner','role')->wherePivot("status","ACTIVE");
	}
	
	public function childs(){
		return $this->hasMany('App\Models\PartnerRelation','parent','code');
	}
		
	public function children(){
		return $this->hasMany("App\Models\PartnerRelation","parent","code");
	}

	public function parent(){
		return $this->hasOne('App\Models\PartnerRelation','partner','code');
	}

	public function parentDetails(){
		return $this->belongsToMany('App\Models\Partner','partner_relations','partner','parent')->with('Roles');
	}
	
	public function industry(){
		return $this->belongsToMany("App\Models\CustomerIndustry","partner_details","partner","industry");
	}
	
	public function editions(){
		return $this->belongsToMany('App\Models\Edition','partner_products','partner','edition')->withPivot('edition')->wherePivot("status","ACTIVE")->whereActive(1);
	}
	
	public function products(){
		return $this->belongsToMany('App\Models\Product','partner_products','partner','product')->withPivot('edition')->wherePivot("status","ACTIVE")->whereActive(1);
	}
	
	public function customerProducts(){
		return $this->belongsToMany('App\Models\Product','customer_registrations','customer','product')->withPivot('seqno','edition','registered_on','requisition','created_at')->whereActive(1);
	}
	
	public function customerEditions(){
		return $this->belongsToMany('App\Models\Edition','customer_registrations','customer','edition')->withPivot('product','seqno','serialno','using_version','lastused_on','downloaded_version','downloaded_on')->whereActive(1);
	}
	
	public function countries(){
		return $this->belongsToMany("App\Models\Country","partner_countries","partner","country");
	}
	
	public function register(){
		return $this->hasMany('App\Models\CustomerRegistration','customer','code');
	}
	
	public function pricelist(){
		return $this->belongsToMany('App\Models\PriceList','partner_details','partner','pricelist')->where('price_lists.status','ACTIVE');
	}
	
	public function AllChildren(){
		$ID = (Auth()->user())?Auth()->user()->partner:Auth()->guard("api")->user()->partner; $Level = 4; $Field = "`partner`"; $Rep = "[MQ]";
		$EQ[] = $MQ = 'SELECT * FROM partner_relations WHERE parent = "'.$ID.'"';
		$SQ = 'SELECT * FROM partner_relations WHERE parent IN ([MQ])';
		for($i=1; $i<$Level; $i++) $EQ[] = $MQ = str_replace($Rep,str_replace("*",$Field,$MQ),$SQ);
		return array_column(DB::select(implode(" UNION ",$EQ)),"partner");
	}

	public function ChildrenWithRole(){
		$IDs = $this->AllChildren();
		$Partners = Partner::with("roles")->whereIn("code",$IDs)->get()->toArray();
		$CR = array();
		foreach($Partners as $PArray){
			$CR[$PArray["code"]] = array_map(function($RoleArray){
				if($RoleArray["status"] == "ACTIVE") return $RoleArray["name"];
			},$PArray["roles"]);
		}
		return $CR;
	}
	
	public function RoleWithChildren(){
		$IDs = $this->AllChildren();
		$Roles = Role::with(["partners"=>function($qry) use ($IDs){
			$qry->wherePivotIn("partner",$IDs);
		}])->has("partners")->get()->toArray();
		$RC = array();
		foreach($Roles as $RArray){
			$RC[$RArray["name"]] = array_map(function($PArray) use ($IDs){
				return [$PArray["code"],$PArray["status"]];
			},$RArray["partners"]);
		}
		return $RC;
	}

	public function state(){
		return $this->belongsToMany("App\Models\State","partner_details","partner","state");
	}

	public function city(){
		return $this->belongsToMany("App\Models\City","partner_details","partner","city");
	}
	
	public function privilage(){
		return $this->hasOne('App\Models\SupportTeamPrivilage','support_team','code');
	}
	
	public function supportteam(){
		return $this->hasOne('App\Models\DistributorSupportTeam','distributor','code');
	}
	
	public function uploads(){
		return $this->hasMany('App\Models\GeneralUpload','customer','code');
	}
	
//	public function role(){
//		return $this->hasMany('App\Models\PartnerRole','partner','code');
//	}

}
