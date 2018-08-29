<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketCurrentStatus extends Model
{
	protected $table = 'ticket_current_status';
	protected $guarded = [];

	
	
	
	public function ticket(){
		return $this->belongsTo('App\Models\Ticket','ticket','code');
	}
	
	public function team(){
		return $this->hasOne('App\Models\TicketSupportTeam','ticket','ticket');
	}
	
	public function user(){
		return $this->BelongsTo('App\Models\Partner','user','code')->select('code','name');
	}
	
	
}
