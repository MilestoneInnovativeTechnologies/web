<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class eBisSubscription extends Model
{
    protected $table = 'ebis_subscriptions';
    protected $guarded = [];

    private static $inactiveDays = 14;

    protected static function boot(){
        parent::boot();
        static::addGlobalScope('active', function (\Illuminate\Database\Eloquent\Builder $builder) {
            $builder->where(function($Q){ $Q->whereNotIn('status',['Cancelled','Inactive']); });
        });
    }

    public function eBis(){ return $this->belongsTo(eBis::class, 'code', 'code'); }

    public static function rearrange(){
        eBisSubscription::where('status','New')->where('start','>',date('Y-m-d'))->update(['status' => 'Upcoming']);
        eBisSubscription::whereIn('status',['Upcoming','New'])->where('start','<=',date('Y-m-d'))->update(['status' => 'Active']);
        eBisSubscription::where('status','Active')->where('end','<',date('Y-m-d'))->update(['status' => 'Expired']);
        eBisSubscription::where('status','Expired')->where('end','<',date('Y-m-d',strtotime("-" .self::$inactiveDays. " days")))->update(['status' => 'Inactive']);
    }
}
