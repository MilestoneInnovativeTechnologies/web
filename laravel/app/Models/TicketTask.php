<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketTask extends Model
{
	
	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('own', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$builder->where(function($Q){ $Q->has('Ticket'); });
		});
	}

	protected $table = 'ticket_tasks';
	protected $primaryKey = 'id';
	public $incrementing = true;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['created_at','updated_at','description'];
	//protected $visible = ['code','name','Details','Logins'/*,'Roles'*/,'Privilage','Defaultst','Customers','Distributors'];
	protected $with = ['Cstatus'];
	protected $appends = ['available_action'];
	public $action = ['view','edit','delete','chngrsp','open','recheck','work','hold','close'];
	public $task_status = ['CREATED','ASSIGNED','OPENED','RECHECK','REASSIGNED','WORKING','HOLD','CLOSED'];
	public $status_actions = [0=>[0,1,2,3],1=>[0,1,2,3,4],2=>[0,5,6],3=>[0,1,2,3],4=>1,5=>[0,6,7,8],6=>[0,6],7=>[0]];
	public $inactive_doesnot_action = [4,5,6,7,8];
	public $hold_predefined_customer_text = ['Awaiting Customer Response','Customer not available'];
	public $current_task_closed_ticket_not_closable_status_text = 'SYSTEM: Current Task Closed, Awaiting Next task to be opened.';

	public function ticket(){
		return $this->belongsTo('App\Models\Ticket','ticket','code');
	}
	
	public function stype(){
		return $this->belongsTo('App\Models\SupportType','support_type','code')->select('code','name');
	}
	
	public function after(){
		return $this->belongsTo('App\Models\TicketTask','after','id')->select('id','seqno','title');
	}
	
	public function cstatus(){
		//return $this->hasOne('App\Models\TaskStatusTrans','task','id')->latest()->latest('id');
		return $this->hasOne('App\Models\TaskCurrentStatus','task','id');
	}
	
	public function status(){
		return $this->hasMany('App\Models\TaskStatusTrans','task','id');
	}
	
	public function responder(){
		return $this->hasOne('App\Models\TaskResponder','task','id')->with('Responder');
	}
	
	public function createdby(){
		return $this->belongsTo('App\Models\Partner','created_by','code')->with('Roles');
	}

	public function getHandleAfterAttribute($value = null){
		if(is_null($value) || trim($value) == '' || empty($value)) return null;
		$taskArray = \App\Http\Controllers\CommonController::ItemIDsExtractFromDB($value);
		return \App\Models\TicketTask::whereIn('id',$taskArray)->whereTicket($this->attributes['ticket'])->with('Cstatus')->get();
	}

	public function getAvailableActionAttribute($value = null){
		if(!$this->status || !$this->Cstatus) return null;
		$status_actions = $this->status_actions[array_search($this->Cstatus->status,$this->task_status)];
		while(!is_array($status_actions)){ $status_actions = $this->status_actions[$status_actions]; }
		$action_keys = ($this->status == "INACTIVE") ? array_diff($status_actions,$this->inactive_doesnot_action) : $status_actions;
		$actions = $this->action; return array_map(function($key)use($actions){ return $actions[$key]; },$action_keys);
	}
	
	public function scopeOwn($Q){
		$user = (\Auth()->user()) ?: \Auth()->guard('api')->user();
		if($user->rolename == 'supportagent')
			return $Q->whereHas('Responder',function($R)use($user){
				$R->whereResponder($user->partner);
			});
		return $Q;
	}

}
