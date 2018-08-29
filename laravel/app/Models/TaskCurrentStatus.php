<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskCurrentStatus extends Model
{

	protected $table = 'task_current_status';
	protected $primaryKey = 'id';
	public $incrementing = true;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = [];
	//protected $visible = ['code','name','Details','Logins'/*,'Roles'*/,'Privilage','Defaultst','Customers','Distributors'];
	//protected $with = [];


	public function ticket(){
		return $this->belongsTo('App\Models\Ticket','ticket','code');
	}

	public function task(){
		return $this->belongsTo('App\Models\TicketTask','task','id');
	}
}
