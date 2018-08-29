<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTeamPrivilage extends Model
{


	protected $table = 'support_team_privilages';
	protected $primaryKey = 'id';
	public $incrementing = true;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = ['id'];
	//protected $hidden = [];
	protected $visible = ['support_team','privilage'];
	//protected $with = ['Details.City.State.Country','Logins','Roles.Details'];
	
	
	public function team(){
		return $this->belongsTo('App\Models\SupportTeam','support_team','code');
	}


}
