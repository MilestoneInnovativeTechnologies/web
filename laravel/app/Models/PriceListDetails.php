<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceListDetails extends Model
{
    //
	protected $fillable = array('pricelist', 'product', 'edition', 'mop', "price", 'mrp', 'currency');
	
	public function pricelist(){
		return $this->belongsTo("App\Models\PriceList","pricelist","code")->whereStatus("ACTIVE");
	}
	
	public function product(){
		return $this->belongsTo("App\Models\Product","product","code")->whereActive("1");
	}
	
	public function edition(){
		return $this->belongsTo("App\Models\Edition","edition","code")->whereActive("1");
	}
}
