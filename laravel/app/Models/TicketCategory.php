<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketCategory extends Model
{


	protected $table = 'ticket_categories';
	protected $primaryKey = 'id';
	public $incrementing = true;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['ticket','category','user','created_at','updated_at'];
	//protected $visible = ['code','name','Parent','Creator'];
	protected $with = ['Specification','Value'];
	
	public function _GETAUTHUSER(){ return (Auth()->user())?:(Auth()->guard("api")->user()); }

	public function category(){
		return $this->belongsTo('App\Models\TicketCategoryMaster','category','code');
	}

	public function specification(){
		return $this->belongsTo('App\Models\TicketCategorySpecification','specification','code');
	}

	public function value(){
		return $this->belongsTo('App\Models\TicketCategorySpecification','value','code')->withoutGlobalScope('spec');
	}

	public function user(){
		return $this->belongsTo('App\Models\Partner','user','code');
	}
	
	public function del($ticket){
		$this->where('ticket',$ticket)->delete();
	}
	
	public function create_new($ticket,$category,$spec_val){
		$this->del($ticket); if(!$spec_val) return;
		$this->add_new($ticket,$category,$spec_val);
	}
	
	public function add_new($ticket,$category,$spec_val){
		if(!is_array($spec_val) || empty($spec_val)) return null;
		$SPEC = new \App\Models\TicketCategorySpecification;
		foreach($spec_val as $spec => $val){
			$MYSPEC = $SPEC->find($spec);
			if($MYSPEC->spec_values->has($val)) $this->store_new($ticket,$category,$spec,$val);
			else $this->store_new($ticket,$category,$spec,null,$val);
		}
	}
	
	protected function store_new($ticket,$category,$specification,$value,$value_text = null,$user = null){
		$user = ($user) ?: $this->_GETAUTHUSER()->partner;
		return $this->create(compact('ticket','category','specification','value','value_text','user'));
	}

	
}
