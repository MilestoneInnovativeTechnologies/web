<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductEditionPackage extends Model
{

		public $incrementing = false;
		protected $primaryKey = 'product';
		protected $fillable = array('product', 'edition', 'package', "created_by");

		public function products(){
			return $this->belongsTo("App\Models\Product","product","code")->whereActive("1");
		}
		public function editions(){
			return $this->belongsTo("App\Models\Edition","edition","code")->whereActive("1");
		}
		public function packages(){
			return $this->belongsTo("App\Models\Package","package","code")->whereActive("1");
		}

		public function product(){
			return $this->products();
		}
		public function edition(){
			return $this->editions();
		}
		public function package(){
			return $this->packages();
		}
}
