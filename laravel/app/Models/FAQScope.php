<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FAQScope extends Model
{
    protected $table = 'faq_scopes';
    protected $guarded = [];

    public function setPublicAttribute($value){ $this->attributes['public'] =  $value ? 'YES' : 'NO'; }
    public function setSupportAttribute($value){ $this->attributes['support'] =  $value ? 'YES' : 'NO'; }
    public function setDistributorAttribute($value){ $this->attributes['distributor'] =  $value ? 'YES' : 'NO'; }
    public function setCustomerAttribute($value){ $this->attributes['customer'] =  $value ? 'YES' : 'NO'; }
    public function setPartnerAttribute($value){ $this->attributes['partner'] =  $value ?: null; }

    public function question(){
        return $this->belongsTo('App\Models\FAQ','question','id');
    }

    public function Partner(){
        return $this->belongsTo('App\Models\Partner','partner','code');
    }
}
