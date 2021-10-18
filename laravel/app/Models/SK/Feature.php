<?php

namespace App\Models\SK;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $table = 'sk_features';
    protected $guarded = [];

    private static function GC($N, $MAX = 25, $SIZE = 1, $ALP = "ABCDEFGHIJKLMNOPQRSTUVWXYZ"){
        $ALPAry = str_split($ALP,1); $LEN = count($ALPAry); $STEP = (1+$MAX)/pow($LEN,$SIZE); $POW = pow($LEN,$SIZE);
        $idx = ($N > $MAX) ? pow($LEN,$SIZE)-1 : intval(floor($N/$STEP)); $MAT = [];
        while($SIZE--) {
            $npow = pow($LEN,$SIZE);
            $MAT[] = intval($idx / $npow); $idx = $idx % $npow;
        }
        return array_reduce($MAT,function($a,$v)use($ALPAry){ return $a . $ALPAry[$v]; },'');
    }

    public static function CODE(){
        $codes = Feature::pluck('code')->toArray(); $code = '';
        $S2 = intval((((date('j')%15)*24)/3.5)+3);
        $F2 = self::GC((((date('z')-1)*24*60*60)+(date('G')*60*60)+(intval(date('i'))*60)+intval(date('s')))%12600,12599,2);
        $T1 = self::GC(date('z'),365);
        do {
            $s2 = str_pad($S2,2,'0',STR_PAD_LEFT);
            $code = $F2 . $s2 . $T1; $S2--;
        } while(in_array($code,$codes));
        return $code;
    }

    public function Parent(){ return $this->belongsTo(Feature::class,'parent','id'); }
    public function Children(){ return $this->hasMany(Feature::class,'parent','id'); }
    public function Editions(){ return $this->hasMany(EditionFeature::class,'feature','id')->where('edition','!=',1); }
}
