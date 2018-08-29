<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductEditionFeature extends Model
{

		public $incrementing = false;
		protected $primaryKey = 'product';
		protected $fillable = array('product', 'edition', 'feature', 'value', 'order', "created_by");

		public function products(){
			return $this->belongsTo("App\Models\Product","product","code")->whereActive("1");
		}
		public function editions($PCode){
			return $this->belongsTo("App\Models\Edition","edition","code")->whereActive("1");
		}
		public function features(){
			return $this->belongsTo("App\Models\Feature","feature","id")->whereActive("1");
		}
}
