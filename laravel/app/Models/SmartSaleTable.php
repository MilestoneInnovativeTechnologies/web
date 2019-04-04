<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmartSaleTable extends Model
{
    public function SmartSale(){
        return $this->belongsTo(SmartSale::class,'smart_sale','id');
    }
}
