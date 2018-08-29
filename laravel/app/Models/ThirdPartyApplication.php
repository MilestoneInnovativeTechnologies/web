<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThirdPartyApplication extends Model
{
	
	protected $table = 'third_party_applications';
	protected $primaryKey = 'code';
	public $incrementing = false;
	public $timestamps = true;
	//protected $fillable = ['code','name','description','class','url','arg1','arg2','arg3','arg4','arg5','arg6','arg7','arg8','arg9'];
	protected $guarded = [];
	//protected $hidden = ['created_at','updated_at'];
	//protected $visible = ['code','name','Details','Logins'/*,'Roles'*/,'Privilage','Defaultst','Customers','Distributors'];
	//protected $with = ['Customer','User'];
	
	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('active', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$builder->where(function($Q){ $Q->where('status','ACTIVE'); });
		});
		static::addGlobalScope('latest', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$builder->latest('updated_at');
		});
		static::addGlobalScope('own', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$user = (\Auth()->user()) ?: Auth()->guard('api')->user();
			if(!$user || !in_array($user->rolename,['supportteam','supportagent','company']))
				$builder->where(function($Q){ $Q->where('public','Yes'); });
		});
	}
	
	public $actions = ['new','file','edit','download'];
	public $conditional_action = [];
	public $role_groups = [[],['company']];
	public $group_actions = [0=>[3],1=>[0,1,2,3]];
	public $default_group = 0;
	public $modal_actions = ['new','download'];

	public $action_title = ['new' => 'Upload new', 'file' => 'Change/Update file', 'edit' => 'Edit Details'];
	public $action_icon = ['new' => 'plus', 'file' => 'file', 'edit' => 'edit'];

	protected function _GETROLEGROUP($rolename){ foreach($this->role_groups as $grp => $names) if(in_array($rolename,$names)) return $grp; return $this->default_group; }
	protected function _GETGROUPACTIONS($group){ return $this->group_actions[$group]; }
	protected function _GETROLEACTIONS($role){ return $this->_GETGROUPACTIONS($this->_GETROLEGROUP($role)); }
	public function _GETARRAYVALUES($array, $keys){ return array_map(function($key)use($array){ return $array[$key]; },$keys); }
	public function _GETAUTHUSER(){ return (Auth()->user())?:(Auth()->guard("api")->user()); }

	protected $appends = ['available_actions'];
	public function getAvailableActionsAttribute($value = null){
		$AuthUser = $this->_GETAUTHUSER();
		$role = ($AuthUser) ? $AuthUser->rolename : 'public';
		$role_actions = $this->_GETROLEACTIONS($role);
		if(!$this->exists) return $this->_GETARRAYVALUES($this->actions,$role_actions);
		$actions = array_filter($role_actions,function($ra){ return ($this->conditional_action && array_key_exists($ra,$this->conditional_action)) ? call_user_func([$this,$this->conditional_action[$ra]],$this) : true; });
		return $this->_GETARRAYVALUES($this->actions,$actions);
	}
	
	protected function setCodeAttribute($Code = NULL){ $this->attributes['code']	=	($Code)?:$this->NewCode(); }
	public function NewCode(){
		$CodePrefixChar = "TPA";
		$TotalCodeLength = 7;
		$LastNum = 2523; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
		$WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
		$LastCode = $this->withoutGlobalScopes()->where($this->primaryKey,"REGEXP",$WhereValue)->max($this->primaryKey);
		if($LastCode) $LastNum = intval(mb_substr($LastCode,$PrefixLength));
		return ($CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT)));
	}
	
	public $storage_disk = 'tpa';
	public $storage_path = '';
	
	public function _validation(){
		$Rule = [
			'code'			=>	'nullable|unique:third_party_applications,code',
			'name'		=>	'required',
		];
		$Message = [
			'code.unique'				=>	'The code is already in use.',
			'name.required'	=>	'Subject is mandatory field.',
		];
		return ['rules' => $Rule, 'messages' => $Message];
	}
	
	public function create_new($code = null, $name, $description = null, $version = null, $vendor_url = null, $public = null, $file = null, $extension = null, $size = null){
		$created_by = ($this->_GETAUTHUSER()) ? $this->_GETAUTHUSER()->partner : null;
		return $this->create(compact('code','name','description','version','vendor_url','file','public','created_by'));
	}
	
	public function data_update($args){
		$this->update($args);
	}

	public function download_url($times = 0){
		$pArray = ['code','name','extension','file','disk','path','author','downloads'];
		$vArray = [$this->code,$this->name,$this->extension,$this->file,$this->storage_disk,$this->storage_path,$this->_GETAUTHUSER()->Partner->name,$times];
		$key = \App\Http\Controllers\KeyCodeController::Encode($pArray, $vArray);
		if($times > 0) $this->create_download_count_file($this->code,$times);
		return Route('tpa.download',[$this->code,$key]);
	}
	
	public function create_download_count_file($code,$times){
		$file = $this->count_file($code);
		\Storage::disk($this->storage_disk)->put($file,$times);
	}
	
	public function count_file($code){
		return $this->storage_path.'/'.$code.'.num';
	}
	
	public function get_download_count($code){
		return intval(\Storage::disk($this->storage_disk)->get($this->count_file($code)));
	}
	
	public function increment_download($code){
		if(!$this->is_downloadable_limit($code)) return;
		$C = $this->get_download_count($code);
		\Storage::disk($this->storage_disk)->put($this->count_file($code),--$C);
		if($C < 1) $this->delete_count_file($code);
		return $C;
	}
	
	public function delete_count_file($code){
		\Storage::disk($this->storage_disk)->delete($this->count_file($code));
	}
	
	public function is_downloadable_limit($code){
		return (\Storage::disk($this->storage_disk)->exists($this->count_file($code)) && $this->get_download_count($code) > 0);
	}
	

}
