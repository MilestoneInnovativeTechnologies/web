<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketStatusTrans extends Model
{

	protected $table = 'ticket_status_trans';
	protected $primaryKey = 'id';
	public $incrementing = true;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['created_at','updated_at'];
	//protected $visible = ['code','name','Details','Logins'/*,'Roles'*/,'Privilage','Defaultst','Customers','Distributors'];
	protected $with = ['User'];

	public function ticket(){
		return $this->belongsTo('App\Models\Ticket','ticket','code')->select('code','title','type','category','priority');
	}
	
	public function user(){
		return $this->belongsTo('App\Models\Partner','user','code')->select('code','name');
	}

}
