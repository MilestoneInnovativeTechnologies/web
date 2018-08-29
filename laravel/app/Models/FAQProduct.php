<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FAQProduct extends Model
{
    protected $table = 'faq_products';
    protected $guarded = [];
    protected $with = ['Product','Edition'];

    public function question(){
        return $this->belongsTo('App\Models\FAQ','question','id');
    }

    public function Product(){
        return $this->belongsTo('App\Models\Product','product','code');
    }
    public function Edition(){
        return $this->belongsTo('App\Models\Edition','edition','code');
    }
}
