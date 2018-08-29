<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceContract extends Model
{
	
	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('latest', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$builder->latest();
		});
		static::addGlobalScope('own', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$user = (\Auth()->user()) ?: \Auth()->guard('api')->user();
			if($user){
				$me = $user->partner;
				if($user->rolename == 'customer'){
					$builder->where(function($Q) use($me){ $Q->where('customer',$me); });
				} else {
					$builder->where(function($Q){ $Q->has('Customer'); });
				}
			}
		});
	}

	protected $table = 'maintenance_contracts';
	protected $primaryKey = 'code';
	public $incrementing = false;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['created_at','updated_at'];
	//protected $visible = [];
	//protected $with = [];
	protected $appends = ['status','available_actions'];

	protected function setCodeAttribute($Code = NULL){
		$this->attributes['code']	=	($Code)?:$this->NewCode();
	}
	
	
	private $status_time = ['ACTIVE' => ['EXPIRE TODAY' => 86400, 'EXPIRE WITHIN ' => 86400*10, 'EXPIRING SOON' => 86400*45],
													'INACTIVE' => ['JUST EXPIRED' => 86400*7, 'EXPIRED ' => 86400*14, 'EXPIRED' => 86400*45]];
	private $actions = ['view','modify','delete','renew','et_mail','es_mail','je_mail','ex_mail'];
	private $status_actions = ['UPCOMING' => [0,1/*,2*/], 'ACTIVE' => [0], 'INACTIVE' => [0,7], 'EXPIRE TODAY' => [0,3,4], 'EXPIRING SOON' => [0,3,5], 'JUST EXPIRED' => [0,3,6], 'EXPIRED' => [0,3,7]];
	private $dyn_status_state = ['EXPIRE WITHIN' => 'EXPIRING SOON', 'EXPIRED' => 'EXPIRED'];
	
	public function NewCode(){
		$TotalCodeLength = 11; $CodePrefixChar = "MC" . date("y") . str_pad(date("z")+1,3,"0",STR_PAD_LEFT) . $this->ALP(date("G"),0,23);
		$LastNum = 0; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
		$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
		$LastCode = $this->withoutGlobalScope('own')->where($this->primaryKey,"REGEXP",$WhereValue)->max($this->primaryKey);//orderBy($this->primaryKey,"desc")->limit(1)->pluck($this->primaryKey);
		if($LastCode) $LastNum = intval(mb_substr($LastCode,$PrefixLength));
		return ($CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT)));
	}
	
	private function ALP($N, $MIN = 1, $MAX = 26, $ALP = "ABCDEFGHIJKLMNOPQRSTUVWXYZ", $SIZE = 1){
		$ALPAry = str_split($ALP,$SIZE); $Step = (1+$MAX-$MIN)/count($ALPAry); 
		$Index = intval(round($N/$Step)); return (array_key_exists($Index,$ALPAry))?$ALPAry[$Index]:$ALPAry[array_rand($ALPAry,1)];
	}

	public function getStatusAttribute($value = null){
		return $this->MCStatus($this->end_time, $this->start_time);
	}

	public function getAvailableActionsAttribute(){
		$Status = $this->status; $action_keys = $this->GetStatusActions($Status);
		return $this->GetArrayValues($this->actions, $action_keys);
	}
	private function GetStatusActions($Status){
		$ASA = $this->status_actions; $MES = false;
		if(!array_key_exists($Status, $ASA)){
			$DSS = $this->dyn_status_state; foreach($DSS as $Key => $State){ if($MES === false && mb_substr($Status,0,strlen($Key)) == $Key) $MES = $State; }
		} else $MES = $Status;
		return $this->GetStatusActionKeys($MES);
	}
	private function GetStatusActionKeys($Status){
		$ASA = $this->status_actions; $MSA = $ASA[$Status];
		if(is_array($MSA)) return $MSA;
		return $this->GetStatusActionKeys($MSA);
	}
	private function GetArrayValues($Ary, $Keys){
		return array_map(function($Key)use($Ary){ return $Ary[$Key]; },$Keys);
	}

	private function MCStatus($endTime, $startTime){
		$currentTime = time(); if($startTime > $currentTime) return 'UPCOMING';
		$OneMinute = 60; $OneHour = $OneMinute * 60; $OneDay = $OneHour * 24;
		$Activity = ($endTime >= $currentTime) ? ['ACTIVE',($endTime - $currentTime),' DAYS'] : ['INACTIVE',($currentTime - $endTime),' DAYS AGO'];
		foreach($this->status_time[$Activity[0]] as $Status => $Time)
			if($Activity[1] <= $Time) return (mb_substr($Status,-1) == " ") ? ($Status . floor($Activity[1]/$OneDay) . $Activity[2]) : $Status;
		return $Activity[0];
	}
	
	public function registration(){
		return $this->hasMany('App\Models\CustomerRegistration','customer','customer')->select('customer','seqno','product','edition','registered_on')->with(['Product' => function($Q){ $Q->select('code','name'); }, 'Edition' => function($Q){ $Q->select('code','name'); }]);
	}
	
	public function customer(){
		return $this->belongsTo('App\Models\Customer','customer','code')->select('code','name');
	}
	
	public function renewed(){
		return $this->belongsTo('App\Models\MaintenanceContract','renewed_to','code')->select('code','customer','start_time','end_time','amount_actual','amount_paid');
	}
	
	public function scopeActive($Q){
		return $Q->where('end_time','>=',time())->where('start_time','<',time())->oldest('end_time');
	}
	
	public function scopeExpsoon($Q){
		return $Q->active()->where('end_time','<',time()+(86400*10));
	}
	
	public function scopeInactive($Q){
		return $Q->where('end_time','<',time()-(86400*7))->latest('end_time');
	}
	
	public function scopeExpired($Q){
		return $Q->where('end_time','<',time()-(86400*7))->latest('end_time');
	}
	
	public function scopeJustexp($Q){
		return $Q->where('end_time','<',time())->where('end_time','>=',time()-(86400*7));
	}
	
	public function scopeUpcoming($Q){
		return $Q->where('start_time','>',time())->oldest('start_time');
	}
	
}