<?php

namespace App\Models\SK;

use App\Http\Controllers\KeyCodeController;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $table = 'sk_subscriptions';
    protected $guarded = ['code'];

    protected static function boot() {
        parent::boot();
        static::creating(function($sub){
            $Branch = Branch::with(['Client','Edition','Features'])->find($sub->branch);
            $sub->setAttribute('edition',$Branch->edition);
            $sub->setAttribute('expiry',Carbon::parse($sub->expiry)->endOfDay()->toDateTimeString());
            $sub->setAttribute('code',self::code($Branch->Client->code,$Branch->Client->name,$Branch->code,$Branch->name,$Branch->Edition->name,$Branch->Features,$Branch->key,$sub->expiry));
            $sub->setAttribute('code_date',Carbon::now()->toDateTimeString());
        });
    }

    public static function key($serial,$date){
        $s2t = strtotime($date);
        return date('y',$s2t) . md5(date('y',$s2t) . $serial . date('md',$s2t)) . date('md',$s2t);
    }

    public static function code($client_code,$client,$branch_code,$branch,$edition,$Features,$key,$expiry){
        $features = self::featureArray($Features); $PAry = array_keys($features) ?: []; $VAry = array_values($features) ?: [];
        $feature_encrypt = KeyCodeController::Encode($PAry,$VAry);
        $timestamp = Carbon::parse($expiry)->getTimestamp();
        $key_length = strlen($key); $separate = intval(floor(strlen($key)/2)); $i = strlen($timestamp) - 2; $portions = [];
        while($i > 0) {
            if(intval(substr($timestamp,$i,2)) <  $key_length){
                $separate = substr($timestamp,$i,2) . ""; break;
            } $i--;
        }
        $portions[] = $separate; $portions[] = md5(substr($key,0,intval($separate))); $portions[] = substr($timestamp,0,5);
        $portions[] = $feature_encrypt; $portions[] = md5(substr($key,intval($separate))); $portions[] = substr($timestamp,5);
        $portions[] = "/"; $portions[] = base64_encode(json_encode(compact('client','branch','edition','client_code','branch_code','expiry')));
        return implode("",$portions);
    }

    protected static function featureArray($Features){
        return $Features->mapWithKeys(function($feature){ return [$feature->code => $feature->pivot->value]; })->toArray();
    }

    public function Edition(){ return $this->belongsTo(Edition::class,'edition','id'); }

}
