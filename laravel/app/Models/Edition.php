<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Edition extends Model
{
	
		protected static function boot(){
			parent::boot();
			$user = (\Auth()->user()) ?: \Auth()->guard('api')->user();
			if($user){
				static::addGlobalScope('own', function (\Illuminate\Database\Eloquent\Builder $builder) use($user) {
					$role = $user->rolename; $EDNCodes = [];
					if($role == 'customer'){ $EDNCodes = \App\Models\CustomerRegistration::whereCustomer($user->partner)->pluck('edition')->toArray(); }
					if($role == 'dealer' || $role == 'distributor') { $EDNCodes = \App\Models\PartnerProduct::wherePartner($user->partner)->pluck('edition')->toArray(); }
					if(!empty($EDNCodes)) $builder->whereIn('code',$EDNCodes);
				});
			}
			static::addGlobalScope('active', function (\Illuminate\Database\Eloquent\Builder $builder) {
				$builder->whereActive('1');
			});
		}

		public $incrementing = false;
		protected $primaryKey = 'code';
		protected $fillable = array('code', 'name', 'private', 'description_public', 'description_internal', "created_by");
		
		public function NextCode(){
			$CodePrefixChar = "EDN";
			$TotalCodeLength = 6;
			$LastNum = 0; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
			$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
			$LastCode = $this->where("code","REGEXP",$WhereValue)->orderBy("code","desc")->limit(1)->pluck("code");
			if(!empty($LastCode[0]))
				$LastNum = intval(mb_substr($LastCode[0],$PrefixLength));
			return $CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT));
		}
		
		public function GetMyFillableFields(){
			return $this->fillable;
		}
		
		public function MyValidationRules(){
			return array(
				"code"	=>	"required|unique:editions,code",
				"name"	=>	"required|unique:editions,name",
			);
		}
		
		public function products(){
			return $this->belongsToMany("App\Models\Product","products_editions","edition","product")->whereActive("1")->withPivot('level', 'description')->withTimestamps();
		}
		
		public function features(){
			return $this->belongsToMany("App\Models\Feature","product_edition_features","edition","feature")->whereActive("1")->withPivot('product', 'value', 'order')->withTimestamps();
		}
		
		public function packages(){
			return $this->belongsToMany("App\Models\Package","product_edition_packages","edition","package")->whereActive("1")->withPivot('product')->withTimestamps();
		}
}
