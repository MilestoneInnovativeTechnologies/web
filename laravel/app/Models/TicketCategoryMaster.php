<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketCategoryMaster extends Model
{
	
	protected $table = 'ticket_category_masters';
	protected $primaryKey = 'code';
	public $incrementing = false;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['created_at','updated_at'];
	//protected $visible = ['code','name','Details','Logins'/*,'Roles'*/,'Privilage','Defaultst','Customers','Distributors'];
	//protected $with = ['Customer','User'];
	
	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('active', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$builder->where(function($Q){ $Q->where('status','ACTIVE'); });
		});
		static::addGlobalScope('own', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$builder->where(function($Q){ $Q->where('available','ALWAYS'); });
		});
	}
	
	public $actions = ['add','edit','delete','specs','activate'];
	public $conditional_action = [1 => '_isActive', 2 => '_isActive', 3 => '_isActive', 4 => '_isInactive'];
	public $list_actions = [1,2,3,4];

	public function _GETARRAYVALUES($array, $keys){ return array_map(function($key)use($array){ return $array[$key]; },$keys); }
	public function _GETAUTHUSER(){ return (Auth()->user())?:(Auth()->guard("api")->user()); }
	
	private function _isActive($Model){ return $Model->status == 'ACTIVE'; }
	private function _isInactive($Model){ return $Model->status == 'INACTIVE'; }
	
	public $ondemand_file_path = 'ticket/ondemandcategory';
	public $ondemand_store_disk = 'local';

	protected $appends = ['available_actions','specs'];
	public function getAvailableActionsAttribute($value = null){
		$actions = array_filter(array_keys($this->actions),function($ak){ return ($this->conditional_action && array_key_exists($ak,$this->conditional_action)) ? call_user_func([$this,$this->conditional_action[$ak]],$this) : true; });
		return $this->_GETARRAYVALUES($this->actions,$actions);
	}
	public function getSpecsAttribute($value = null){
		if(!$this->specifications) return null;
		return \App\Models\TicketCategorySpecification::find($this->get_spec($this->specifications));
	}
	
	protected function setCodeAttribute($Code = NULL){ $this->attributes['code']	=	($Code)?:$this->NewCode(); }
	public function NewCode(){
		$CodePrefixChar = "TC";
		$TotalCodeLength = 5;
		$LastNum = 0; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
		$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
		$LastCode = $this->withoutGlobalScopes()->where($this->primaryKey,"REGEXP",$WhereValue)->max($this->primaryKey);
		if($LastCode) $LastNum = intval(mb_substr($LastCode,$PrefixLength));
		return ($CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT)));
	}
	
	public $action_title = ['add' => 'Add new category','edit' => 'Edit','delete' => 'Delete','specs' => 'Assign/Unassign Specifications','activate' => 'Make this ACTIVE'];
	public $action_icon = ['add' => 'plus','edit' => 'edit','delete' => 'remove','specs' => 'th-list','activate' => 'flash'];
	public $priority_field_options = ['NORMAL','LOW','HIGH','VERY LOW','VERY HIGH'];
	public $available_field_options = ['ALWAYS' => 'ALWAYS', 'isPresale' => 'On Presale Period', 'ifSupportTeam' => 'For Support Team Only', 'onDemand' => 'On Demand Only'];
	public $public_availables = ['ALWAYS'];
	
	public function validation_rules(){
		$Rules = [
			'code'	=>	'nullable|unique:ticket_category_masters,code',
			'name'	=>	'required',
			'priority'	=>	['required',\Illuminate\Validation\Rule::in($this->priority_field_options)],
			'available'	=>	['required',\Illuminate\Validation\Rule::in(array_keys($this->available_field_options))],
		];
		$Messages = [
			'code.unique' => 'The Code entered is already taken, Please try another code',
			'name.required' => 'The Name is mandatory field.',
			'priority.required' => 'Priority is required field.',
			'priority.in' => 'Priority selected is not available right now.',
			'available.required' => 'Available is required field.',
			'available.in' => 'Selected Available field option is not available right now.',
		];
		return ['rules' => $Rules, 'messages' => $Messages];
	}
	
	public function add_new($code = null, $name, $priority = 'NORMAL', $available = 'ALWAYS', $description = null, $status = 'ACTIVE', $created_by = null, $specifications = null){
		$code = ($code)?:$this->NewCode(); $created_by = ($created_by) ?: $this->_GETAUTHUSER()->partner;
		return $this->create(compact('code','name','description','priority','available','status','created_by','specifications'));
	}
	
	//----------------------------------------------------------------------------------------------------
	public function get_spec($spec, $method = 'decode'){
		if(empty($spec)) return null;
		if($method == 'decode') return explode("-",mb_substr($spec,1,-1));
		else return "-" . implode("-",$spec) . "-";
	}
	
	//----------------------------------------------------------------------------------------------------
	public function get_ondemand_file($C,$S){
		return $this->ondemand_file_path.'/'.$C.'.'.$S.'.json';
	}
	public function ondemand_exists($C,$S,$T){
		$file = $this->get_ondemand_file($C,$S);
		return (\Storage::disk($this->ondemand_store_disk)->exists($file) && in_array($T,json_decode(\Storage::disk($this->ondemand_store_disk)->get($file),true)))?:false;
	}
	public function ondemand_add($C,$S,$T){
		if($this->ondemand_exists($C,$S,$T)) return;
		$file = $this->get_ondemand_file($C,$S);
		if(\Storage::disk($this->ondemand_store_disk)->exists($file)) return \Storage::put($file,json_encode(array_merge($this->ondemand_get($C,$S),[$T])));
		return \Storage::put($file,json_encode([$T]));
	}
	public function ondemand_delete($C,$S,$T){
		if(!$this->ondemand_exists($C,$S,$T)) return;
		$file = $this->get_ondemand_file($C,$S); $content = $this->ondemand_get($C,$S);
		if(count($content) < 2) return \Storage::disk($this->ondemand_store_disk)->delete($file);
		return \Storage::put($file,json_encode(array_diff($content,[$T])));
	}
	public function ondemand_get($C,$S){
		$file = $this->get_ondemand_file($C,$S); if(!\Storage::disk($this->ondemand_store_disk)->exists($file)) return null;
		return json_decode(\Storage::disk($this->ondemand_store_disk)->get($file),true);
	}
	
	
	
	
	
	
	
	//----------------------------------------------------------------------------------------------------
	public function ALWAYS(){
		return $this->get()->pluck('name','code')->toArray();
	}
	
	public function ifSupportTeam($Whom){
		return ($Whom->rolename == 'supportteam' || $Whom->rolename == 'supportagent') ? $this->withoutGlobalScope('own')->where('available','ifSupportTeam')->get()->pluck('name','code')->toArray() : [];
	}
	
	public function isPresale($For){
		if(!$For) return [];
		$Date = ($For->presale_extended_to)?:$For->presale_enddate; if(!$Date || (strtotime($Date) < strtotime(date('Y-m-d 00:00:00')))) return [];
		return $this->withoutGlobalScope('own')->where('available','isPresale')->pluck('name','code')->toArray();
	}
	
	public function onDemand($For){
		if(!$For) return [];
		return $this->withoutGlobalScope('own')->whereIn('available',['onDemand','isPresale'])->get()->filter(function($item,$key) use($For){
			return $this->ondemand_exists($For->customer,$For->seqno,$item->code);
		})->pluck('name','code')->toArray();
	}






	//----------------------------------------------------------------------------------------------------
	public function excludedDistributors(){
		return $this->hasMany("App\Models\DistributorExcludeCategory","category",'code');
	}





}
