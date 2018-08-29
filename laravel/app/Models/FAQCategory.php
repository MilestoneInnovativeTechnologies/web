<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FAQCategory extends Model
{
    protected $table = 'faq_categories';
    protected $guarded = [];

    public function getCategoriesAttribute($value){
        return ($value) ? explode("-",mb_substr($value,1,-1)) : [];
    }
    public function setCategoriesAttribute($data = null){
        $this->attributes['categories'] = is_array($data) ? ("-" . implode("-",$data) . "-") : ( $data ? ("-".$data."-") : null );
    }

}
