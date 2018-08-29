<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketCoversation extends Model
{
	
	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('own', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$builder->whereIn('ticket',\App\Models\Ticket::pluck('code')->toArray());
		});
	}

	protected $table = 'ticket_coversations';
	protected $primaryKey = 'id';
	public $incrementing = true;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['updated_at'];
	//protected $visible = ['code','name','Details','Logins'/*,'Roles'*/,'Privilage','Defaultst','Customers','Distributors'];
	//protected $with = ['Ticket','User'];
	
	public function ticket(){
		return $this->belongsTo('App\Models\Ticket','ticket','code');
	}

	public function user(){
		return $this->belongsTo('App\Models\Partner','user','code');
	}

}
