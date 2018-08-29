<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Auth;
use DB;
use App\Models\Role;
use App\Models\Partner;

class User extends Authenticatable
{
    use Notifiable;
		
		protected $appends = ['rolename'];
		
		protected $table = "partner_logins";
	
		public function getRolenameAttribute($value = null)
    {
				if(session()->has('_rolename')) return session()->get('_rolename');
				return implode(',',$this->Role->pluck('rolename')->toArray());
    }
		
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'partner', 'email', 'password', 'api_token', 'status', 'created_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token', 'created_by', 'created_at', 'updated_at'
    ];

	protected $with = ['Role'];
	
	
	public function partner(){
		return $this->belongsTo("App\Models\Partner","partner","code");
	}
	
	public function details(){
		return $this->hasOne("App\Models\PartnerDetails","partner","partner");
	}
	
	public function roles(){
		return $this->belongsToMany("App\Models\Role","partner_roles","login","role")->wherePivot("status","ACTIVE");
	}
	
	public function parent(){
		return $this->hasOne("App\Models\PartnerRelation","partner","partner");
	}
	
	public function children(){
		return $this->hasMany("App\Models\PartnerRelation","parent","partner");
	}

	protected function setPrimaryKey($key)
	{
		$this->primaryKey = $key;
	}	
	
	public function customer_products(){
		$this->setPrimaryKey('partner');
		$relation = $this->belongsToMany("App\Models\Product","customer_registrations","customer","product")->withPivot("edition","seqno","serialno");
		$this->setPrimaryKey('id');
		return $relation;
	}
	
	public function products(){
		$this->setPrimaryKey('partner');
		$relation = $this->hasMany("App\Models\PartnerProduct","partner","partner");//->withPivot("edition");
		$this->setPrimaryKey('id');
		return $relation;
	}
	
	public function editions(){
		$this->setPrimaryKey('partner');
		$relation = $this->belongsToMany("App\Models\Edition","customer_products","customer","edition")->withPivot("product","seqno","using_version","lastused_on");
		$this->setPrimaryKey('id');
		return $relation;
	}
	
	
	
	public function AllChildren(){
		$ID = (Auth::user()->partner)?:Auth::guard("api")->user()->partner; $Level = 4; $Field = "`partner`"; $Rep = "[MQ]";
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
	
	public function presale(){
		return $this->hasOne("App\Models\PresaleCustomer","partner","partner");
	}
	
	public function isPresale(){
		return (bool) $this->presale()->count();
	}
	
	public function countries(){
		$this->setPrimaryKey('partner');
		$relation = $this->belongsToMany("App\Models\Country","partner_countries","partner","country");
		$this->setPrimaryKey('id');
		return $relation;
	}
	
	private function getAuthUser(){
		return (Auth()->user())?:(Auth()->guard("api")->user());
	}
	
	public function role(){
		return $this->hasMany("App\Models\PartnerRole","login","id")->whereStatus("ACTIVE");
	}
	
}

																							
																							
																							
																							
																							
																							
																							
																							
																							
																							
																							
																							
																							
																							
																							
																							
																							
																							
																							
																							
																							