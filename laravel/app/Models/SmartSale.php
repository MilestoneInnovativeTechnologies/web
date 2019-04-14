<?php

namespace App\Models;

use App\Http\Controllers\SmartSaleController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SmartSale extends Model
{
    use AvailableActions;
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
        $LastCode = $this->withoutGlobalScopes()->where('code',"REGEXP",$WhereValue)->max('code');
        if($LastCode) $LastNum = intval(mb_substr($LastCode,$PrefixLength));
        return ($CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT)));
    }

    public $actions = ['view','config','edit'];
    public $conditional_action = [];
    public $role_groups = [['company'],[]];
    public $group_actions = [0=>[0,1,2]];
    public $default_group = 0;
    public $modal_actions = ['add'];

    public $action_title = ['view' => 'View Details', 'edit' => 'Update Details', 'config' => 'Download Config file for synchronizer'];
    public $action_icon = ['view' => 'fullscreen', 'edit' => 'edit', 'config' => 'cog'];

    public function getImageAttribute($image){
        return $image ? Storage::disk(SmartSaleController::$Storage)->url($image) : null;
    }

    public function getProductAttribute(){
        $customer = $this->customer; $seqno = $this->seq;
        $Reg = CustomerRegistration::where(compact('customer','seqno'))->with(['Product','Edition'])->first();
        return $Reg ? implode(" ",[$Reg->Product->name,$Reg->Edition->name,'Edition']) : null;
    }

    public function Customer(){
        return $this->belongsTo(Partner::class,'customer','code');
    }
    public function Tables(){
        return $this->hasMany(SmartSaleTable::class,'smart_sale','id');
    }
}
