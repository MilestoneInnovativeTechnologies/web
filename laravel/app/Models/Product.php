<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
	
		protected static function boot(){
			parent::boot();
			$user = (\Auth()->user()) ?: \Auth()->guard('api')->user();
			if($user){
				static::addGlobalScope('own', function (\Illuminate\Database\Eloquent\Builder $builder) use($user) {
					$role = $user->rolename; $PRDCodes = [];
					if($role == 'customer'){ $PRDCodes = \App\Models\CustomerRegistration::whereCustomer($user->partner)->pluck('product')->toArray(); }
					if($role == 'dealer' || $role == 'distributor') { $PRDCodes = \App\Models\PartnerProduct::wherePartner($user->partner)->pluck('product')->toArray(); }
					if(!empty($PRDCodes)) $builder->whereIn('code',$PRDCodes);
				});
			}
			static::addGlobalScope('active', function (\Illuminate\Database\Eloquent\Builder $builder) {
				$builder->whereActive('1');
			});
		}

		public $incrementing = false;
		protected $primaryKey = 'code';
		protected $fillable = array('code', 'basename', 'name', 'private', 'description_public', 'description_internal', "created_by");
		
		public function NextCode(){
			$CodePrefixChar = "PRD";
			$TotalCodeLength = 6;
			$LastNum = 0; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
			$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
			$LastCode = $this->withoutGlobalScopes()->where("code","REGEXP",$WhereValue)->orderBy("code","desc")->limit(1)->pluck("code");
			if(!empty($LastCode[0]))
				$LastNum = intval(mb_substr($LastCode[0],$PrefixLength));
			return $CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT));
		}
		
		public function GetMyFillableFields(){
			return $this->fillable;
		}
		
		public function MyValidationRules(){
			return array(
				"code"	=>	"required|unique:products,code",
				"name"	=>	"required|unique:products,name",
				"basename"	=>	"required|unique:products,basename",
			);
		}
		
		public function features(){
			return $this->belongsToMany("App\Models\Feature","products_features","product","feature")->whereActive("1")->withPivot('value', 'order')->withTimestamps();
		}
		
		public function editions(){
			return $this->belongsToMany("App\Models\Edition","products_editions","product","edition")->whereActive("1")->withPivot('level', 'description')->withTimestamps();
		}

		public function edition_features(){
			return $this->hasMany("App\Models\ProductEditionFeature","product","code");
		}

		public function edition_packages(){
			return $this->hasMany("App\Models\ProductEditionPackage","product","code");
		}
	
		public function customers(){
			return $this->belongsToMany('App\Models\Partner','customer_registrations','product','customer')->withPivot('edition','seqno','serialno','version');
		}
	
		public function edition_customers($Edition = NULL){
			$return = $this->customers();
			return ($Edition) ? $return->wherePivot("edition",$Edition) : $return;
		}
}
