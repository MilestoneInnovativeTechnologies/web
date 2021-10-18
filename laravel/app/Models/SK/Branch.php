<?php

namespace App\Models\SK;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = 'sk_branches';
    protected $guarded = ['key'];

    protected static function boot() {
        parent::boot();
        static::addGlobalScope('nonCancelled', function (\Illuminate\Database\Eloquent\Builder $builder) {
            $builder->where(function($Q){ $Q->where('status','!=','Cancelled'); });
        });
        static::saving(function($branch){
            if($branch->isDirty('ip_address') && $branch->ip_address) $branch->setAttribute('ip_address_date',Carbon::now()->toDateTimeString());
            if($branch->isDirty('serial')) $branch->setAttribute('key',null);
        });
    }

    public function Features(){ return $this->belongsToMany(Feature::class,'sk_branch_features','branch','feature')->withPivot('value'); }
    public function Client(){ return $this->belongsTo(Client::class,'client','id'); }
    public function Edition(){ return $this->belongsTo(Edition::class,'edition','id'); }
    public function Subscriptions(){ return $this->hasMany(Subscription::class,'branch','id'); }
    public function Subscription(){ return $this->hasOne(Subscription::class,'branch','id')->whereStatus('Active'); }
}
