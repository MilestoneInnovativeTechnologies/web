<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
	protected $table = 'ticket_attachments';
	protected $primaryKey = 'id';
	public $incrementing = true;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['created_at','updated_at','file'];
	//protected $visible = ['code','name','Details','Logins'/*,'Roles'*/,'Privilage','Defaultst','Customers','Distributors'];
	protected $with = ['User'];
	
	public function ticket(){
		return $this->belongsTo('App\Models\Ticket','ticket','code');
	}
	
	public function user(){
		return $this->belongsTo('App\Models\Partner','user','code')->select('code','name');
	}
}
