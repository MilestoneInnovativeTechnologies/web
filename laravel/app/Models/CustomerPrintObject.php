<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPrintObject extends Model
{
	protected $table = 'customer_print_objects';
	protected $primaryKey = 'code';
	public $incrementing = false;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['file','preview','created_at','updated_at'];
	//protected $visible = [];
	protected $with = ['Customer','User','Registration'];
	protected $appends = ['product'];
	
	
	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('active', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$builder->where(function($Q){ $Q->where('status','ACTIVE'); });
		});
		static::addGlobalScope('latest', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$builder->latest();
		});
		static::addGlobalScope('own', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$user = (\Auth()->user()) ?: Auth()->guard('api')->user(); if($user){
				$builder->where(function($Q){ $Q->has('Customer'); });
			}
		});
	}
	
	
	
	protected function setCodeAttribute($Code = NULL){ $this->attributes['code']	=	($Code)?:$this->NewCode(); }
	public function NewCode(){
		$CodePrefixChar = "CPO" . date("y") . str_pad(date("W"),2,"0",STR_PAD_LEFT) . date("N") . $this->ALP(date("G"),0,23) . $this->ALP(date("i"),0,59);
		$TotalCodeLength = 12;
		$LastNum = 0; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
		$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
		$LastCode = $this->withoutGlobalScopes()->where($this->primaryKey,"REGEXP",$WhereValue)->max($this->primaryKey);
		if($LastCode) $LastNum = intval(mb_substr($LastCode,$PrefixLength));
		return ($CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT)));
	}
	
	private function ALP($N, $MIN = 1, $MAX = 26, $ALP = "ABCDEFGHIJKLMNOPQRSTUVWXYZ", $SIZE = 1){
		$ALPAry = str_split($ALP,$SIZE); $Step = (1+$MAX-$MIN)/count($ALPAry); 
		$Index = intval(round($N/$Step)); return (array_key_exists($Index,$ALPAry))?$ALPAry[$Index]:$ALPAry[array_rand($ALPAry,1)];
	}
	
	public function registration(){
		return $this->hasMany('App\Models\CustomerRegistration','customer','customer')->withoutGlobalScope('active')->select('customer','seqno','product','edition','registered_on')->with(['Product' => function($Q){ $Q->select('code','name'); }, 'Edition' => function($Q){ $Q->select('code','name'); }]);
	}
	
	public function customer(){
		return $this->belongsTo('App\Models\Customer','customer','code')->select('code','name');
	}
	
	public function user(){
		return $this->belongsTo('App\Models\Partner','user','code')->select('code','name');
	}

	public function getProductAttribute(){
		$Reg = $this->Registration->toArray(); $Seq = $this->reg_seq;
		foreach($Reg as $PRData)
			if($PRData['seqno'] == $Seq)
				return [$PRData['product']['name'],$PRData['edition']['name']];
	}
}