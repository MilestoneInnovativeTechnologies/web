<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerLogin extends Model
{


    protected $fillable = [
        'partner', 'email', 'password', 'api_token', 'status', 'created_by'
    ];
	//protected $guarded  = ["created_at"];
	
	
	public function partner(){
		return $this->belongsTo('App\Models\Partner','partner','code');
	}
	
	public function roles(){
		return $this->hasMany('App\Models\PartnerRole','login','id');
	}
	
}
