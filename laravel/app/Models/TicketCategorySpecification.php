<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketCategorySpecification extends Model
{
	
	protected $table = 'ticket_category_specifications';
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
		static::addGlobalScope('spec', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$builder->where(function($Q){ $Q->whereNull('spec'); });
		});
	}
	
	public $actions = ['add','edit','delete','activate'];
	public $conditional_action = [1 => '_isActive', 2 => '_isActive', 3 => '_isInactive'];
	public $list_actions = [1,2,3];

	public function _GETARRAYVALUES($array, $keys){ return array_map(function($key)use($array){ return $array[$key]; },$keys); }
	public function _GETAUTHUSER(){ return (Auth()->user())?:(Auth()->guard("api")->user()); }
	
	private function _isActive($Model){ return $Model->status == 'ACTIVE'; }
	private function _isInactive($Model){ return $Model->status == 'INACTIVE'; }

	protected $appends = ['available_actions','spec_values'];
	public function getAvailableActionsAttribute($value = null){
		$actions = array_filter(array_keys($this->actions),function($ak){ return ($this->conditional_action && array_key_exists($ak,$this->conditional_action)) ? call_user_func([$this,$this->conditional_action[$ak]],$this) : true; });
		return $this->_GETARRAYVALUES($this->actions,$actions);
	}
	public function getSpecValuesAttribute($value = null){
		if($this->type == 'VALUE') return null;
		return $this->withoutGlobalScope('spec')->where(function($Q){ $Q->whereNotNull('spec'); })->where('spec',$this->code)->get()->keyBy('code');
	}
	
	protected function setCodeAttribute($Code = NULL){ $this->attributes['code']	=	($Code)?:$this->NewCode(); }
	public function NewCode(){
		$CodePrefixChar = "TCS";
		$TotalCodeLength = 6;
		$LastNum = 0; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
		$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
		$LastCode = $this->withoutGlobalScopes()->where($this->primaryKey,"REGEXP",$WhereValue)->max($this->primaryKey);
		if($LastCode) $LastNum = intval(mb_substr($LastCode,$PrefixLength));
		return ($CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT)));
	}

	public $action_title = ['add' => 'Add new category','edit' => 'Edit','delete' => 'Delete','activate' => 'Make this as Active'];
	public $action_icon = ['add' => 'plus','edit' => 'edit','delete' => 'remove','activate' => 'flash'];
	public $type_field_options = ['SPEC' => 'SPEC','VALUE' => 'SPEC Value'];
	
	public function validation_rules(){
		$Rules = [
			'code'	=>	'nullable|unique:ticket_category_masters,code',
			'name'	=>	'required',
			'type'	=>	['required',\Illuminate\Validation\Rule::in(array_keys($this->type_field_options))],
			'spec'	=>	'required_if:type,VALUE',
		];
		$Messages = [
			'code.unique' => 'The Code entered is already taken, Please try another code',
			'name.required' => 'The Name is mandatory field.',
			'type.required' => 'Specification type selected is not valid.',
			'type.in' => 'Specification type selected is not valid.',
			'spec.required_if' => 'For value type specification, choose proper specification.',
		];
		return ['rules' => $Rules, 'messages' => $Messages];
	}
	
	public function add_new($code = null, $name, $type, $description = null, $spec = null, $status = 'ACTIVE', $created_by = null){
		$code = ($code) ?: $this->NewCode();
		$created_by = ($created_by) ?: $this->_GETAUTHUSER()->partner;
		return $this->create(compact('code','name','description','type','spec','status','created_by'));
	}
	
	public function del(){
		$UpdateArray = ['status' => 'INACTIVE'];
		$this->update($UpdateArray);
		$this->where('spec',$this->code)->update($UpdateArray);
	}
	
	public function activate(){
		$UpdateArray = ['status' => 'ACTIVE'];
		$this->update($UpdateArray);
		$this->withoutGlobalScopes()->where('spec',$this->code)->update($UpdateArray);
	}
}
