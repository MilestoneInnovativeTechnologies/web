<?php

namespace App\Models\SK;

use Illuminate\Database\Eloquent\Model;

class Edition extends Model
{
    protected $table = 'sk_editions';
    protected $guarded = [];

    public function Features(){ return $this->hasMany(EditionFeature::class,'edition','id'); }
}
