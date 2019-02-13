<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PD extends Model
{
    use AvailableActions;

    protected $table = 'pd';
    protected $with = ['Customer'];
    protected $appends = ['product','available_actions'];
    protected $guarded = [];
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('active', function (\Illuminate\Database\Eloquent\Builder $builder) {
            $builder->where(function($Q){ $Q->where('status','Active'); });
        });
    }
    protected function setCodeAttribute($Code = NULL){
        $this->attributes['code']	=	($Code)?:$this->NewCode();
    }
    private function ALP($N, $MIN = 1, $MAX = 26, $ALP = "ABCDEFGHIJKLMNOPQRSTUVWXYZ", $SIZE = 1){
        $ALPAry = str_split($ALP,$SIZE); $Step = (1+$MAX-$MIN)/count($ALPAry);
        $Index = intval(round($N/$Step)); return (array_key_exists($Index,$ALPAry))?$ALPAry[$Index]:$ALPAry[array_rand($ALPAry,1)];
    }
    public function NewCode(){
        $CodePrefixChar = date("y").$this->ALP(date("W"),1,52).$this->ALP(floor(date("j")/2),0,15); $TotalCodeLength = 6;
        $LastNum = 0; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
        $WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
        $LastCode = $this->withoutGlobalScopes()->where('code',"REGEXP",$WhereValue)->max('code');//orderBy($this->primaryKey,"desc")->limit(1)->pluck($this->primaryKey);
        if($LastCode) $LastNum = intval(mb_substr($LastCode,$PrefixLength));
        return ($CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT)));
    }

    public $actions = ['view','edit'];
    public $conditional_action = [];
    public $role_groups = [['company'],[]];
    public $group_actions = [0=>[0,1]];
    public $default_group = 0;
    public $modal_actions = ['add'];

    public $action_title = ['view' => 'View Details', 'edit' => 'Update Details'];
    public $action_icon = ['view' => 'fullscreen', 'edit' => 'edit'];

    public function getProductAttribute(){
        $customer = $this->customer; $seqno = $this->seq;
        $Reg = CustomerRegistration::where(compact('customer','seqno'))->with(['Product','Edition'])->first();
        return $Reg ? implode(" ",[$Reg->Product->name,$Reg->Edition->name,'Edition']) : null;
    }

    public function Customer(){
        return $this->belongsTo(Partner::class,'customer','code');
    }
    public function Tables(){
        return $this->hasMany(PDTable::class,'pd','id');
    }
}
