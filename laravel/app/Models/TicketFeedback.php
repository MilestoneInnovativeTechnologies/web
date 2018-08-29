<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketFeedback extends Model
{

	protected $table = 'ticket_feedbacks';
	protected $primaryKey = 'id';
	public $incrementing = true;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['created_at','updated_at'];
	//protected $visible = ['code','name','Details','Logins'/*,'Roles'*/,'Privilage','Defaultst','Customers','Distributors'];
	//protected $with = [];

	public function ticket(){
		return $this->belongsTo('App\Models\Ticket','ticket','code')->select('code','title')->select('code','title','type','category','priority');
	}

	public function customer(){
		return $this->belongsTo('App\Models\Partner','customer','code')->select('code','name');
	}

}
