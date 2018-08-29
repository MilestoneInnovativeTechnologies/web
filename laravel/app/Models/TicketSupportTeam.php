<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketSupportTeam extends Model
{

	protected $table = 'ticket_support_teams';
	protected $primaryKey = 'id';
	public $incrementing = true;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['created_at','updated_at'];
	//protected $visible = ['code','name','Details','Logins'/*,'Roles'*/,'Privilage','Defaultst','Customers','Distributors'];
	protected $with = ['Team','Customer'];

	public function ticket(){
		return $this->belongsTo('App\Models\Ticket','ticket','code')->select('code','title','ticket_type','category','priority');
	}
	
	public function customer(){
		return $this->belongsTo('App\Models\Customer','customer','code')->select('code','name');
	}
	
	public function team(){
		return $this->belongsTo('App\Models\Partner','team','code')->select('code','name');
	}

}
