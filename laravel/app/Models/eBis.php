<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class eBis extends Model
{
    use AvailableActions;

    protected $table = 'ebis';
    protected $guarded = [];

    protected function setCodeAttribute($Code = NULL){ $this->attributes['code'] = $Code ?: $this->NewCode(); }
    private function ALP($N, $MIN = 1, $MAX = 26, $ALP = "ABCDEFGHIJKLMNOPQRSTUVWXYZ", $SIZE = 1){
        $ALPAry = str_split($ALP,$SIZE); $Step = (1+$MAX-$MIN)/count($ALPAry);
        $Index = intval(round($N/$Step)); return (array_key_exists($Index,$ALPAry))?$ALPAry[$Index]:$ALPAry[array_rand($ALPAry,1)];
    }
    public function NewCode(){
        $CodePrefixChar = date("y").$this->ALP(date("W"),1,52).$this->ALP(floor(date("j")/2),0,15); $TotalCodeLength = 6;
        $LastNum = 0; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
        $WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
        $LastCode = $this->withoutGlobalScopes()->where('code',"REGEXP",$WhereValue)->max('code');
        if($LastCode) $LastNum = intval(mb_substr($LastCode,$PrefixLength));
        return ($CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT)));
    }

    public $actions = ['view','delete','add'];
    public $conditional_action = [];
    public $role_groups = [['company'],[]];
    public $group_actions = [0=>[0,1,2]];
    public $default_group = 0;
    public $modal_actions = [];

    public $action_title = ['view' => 'View Details', 'delete' => 'Remove Client', 'add' => 'Add new subscription'];
    public $action_icon = ['view' => 'fullscreen','delete' => 'trash', 'add' => 'briefcase'];

    public function Subscriptions(){ return $this->hasMany(eBisSubscription::class,'code','code'); }
    public function Customer(){ return $this->belongsTo(Partner::class,'customer','code'); }
}
