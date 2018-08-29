<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskStatusTrans extends Model
{

	protected $table = 'task_status_trans';
	protected $primaryKey = 'id';
	public $incrementing = true;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['created_at','updated_at'];
	//protected $visible = ['code','name','Details','Logins'/*,'Roles'*/,'Privilage','Defaultst','Customers','Distributors'];
	//protected $with = [];

	public function ticket(){
		return $this->belongsTo('App\Models\Ticket','ticket','code')->select('code','title','type','category','priority');
	}

	public function task(){
		return $this->belongsTo('App\Models\TicketTask','task','id')->select('id','ticket','seqno','title');
	}
	
	public function user(){
		return $this->belongsTo('App\Models\Partner','user','code');
	}

}
