<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralUpload extends Model
{
	protected $table = 'general_uploads';
	protected $primaryKey = 'code';
	public $incrementing = false;
	public $timestamps = true;
	//protected $fillable = [];
	protected $guarded = [];
	protected $hidden = [];
	//protected $visible = [];
	protected $with = ['Customer','CreatedBy'];
	public $appends = ['form','download'];
	
	public $upload_disk = 'generalupload';
	
	public function customer(){
		return $this->belongsTo('App\Models\Partner','customer','code');
	}
	
	public function createdBy(){
		return $this->belongsTo('App\Models\Partner','created_by','code')->select('code','name');
	}
	
	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('active', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$builder->where(function($Q){ $Q->whereDeleted('N'); });
		});
		static::addGlobalScope('latest', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$builder->latest('updated_at');
		});
		static::addGlobalScope('limit', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$builder->take(5);
		});
	}
	
	protected function setCodeAttribute($Code = NULL){ $this->attributes['code']	=	($Code)?:$this->NewCode(); }
	public function NewCode(){
		$CodePrefixChar = "GUF" . mt_rand(312,959) . str_pad(date("W"),2,"0",STR_PAD_LEFT) . date("N") . $this->ALP(date("G"),0,23) . $this->ALP(date("i"),0,59) . $this->ALP(date("s"),0,59);
		$TotalCodeLength = 15;
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
	
	public function getFormAttribute(){
		$PAry = ['name','description','code']; $VAry = [$this->name,$this->description,$this->code];
		$Key = \App\Http\Controllers\KeyCodeController::Encode($PAry, $VAry);
		return Route('general.uploadform',$Key);
	}
	
	public function getDownloadAttribute(){
		$PAry = ['name','description','code']; $VAry = [$this->name,$this->description,$this->code];
		$Key = \App\Http\Controllers\KeyCodeController::Encode($PAry, $VAry);
		return Route('download.generalform.uploaded',$Key);
	}
}
