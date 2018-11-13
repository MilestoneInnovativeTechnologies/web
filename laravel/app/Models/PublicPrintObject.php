<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicPrintObject extends Model
{

    protected $table = 'public_print_objects';
    protected $primaryKey = 'code';
    public $incrementing = false;
    public $timestamps = true;
    //protected $fillable = ['code','name','description','class','url','arg1','arg2','arg3','arg4','arg5','arg6','arg7','arg8','arg9'];
    protected $guarded = [];
    //protected $hidden = ['created_at','updated_at'];
    //protected $visible = ['code','name','Details','Logins'/*,'Roles'*/,'Privilage','Defaultst','Customers','Distributors'];
    protected $with = ['Specs'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('active', function (\Illuminate\Database\Eloquent\Builder $builder) {
            $builder->where(function($Q){ $Q->where('status','ACTIVE'); });
        });
        static::addGlobalScope('latest', function (\Illuminate\Database\Eloquent\Builder $builder) {
            $builder->latest('updated_at');
        });
    }

    public $actions = ['new','view','edit','download','delete','change_preview','change_file'];
    public $conditional_action = [];
    public $role_groups = [['supportteam','supportagent','company'],[]];
    public $group_actions = [0=>[0,1,2,3,4,5,6],1=>[3]];
    public $default_group = 1;
    public $modal_actions = ['new'];

    public $action_title = ['new' => 'Add New', 'view' => 'View detail', 'edit' => 'Edit details', 'download' => 'Download this print object', 'delete' => 'Delete this print object', 'change_preview' => 'Update preview', 'change_file' => 'Update File'];
    public $action_icon = ['new' => 'plus', 'view' => 'list-alt', 'edit' => 'edit', 'download' => 'download', 'delete' => 'remove', 'change_preview' => 'picture', 'change_file' => 'file'];

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
        $CodePrefixChar = "PPO";
        $TotalCodeLength = 7;
        $LastNum = 2523; $PrefixLength = strlen($CodePrefixChar); $NumberLength = $TotalCodeLength - $PrefixLength;
        $WhereValue = "^" . $CodePrefixChar . "[0-9]{" . $NumberLength . "}$";
        $LastCode = $this->withoutGlobalScopes()->where($this->primaryKey,"REGEXP",$WhereValue)->max($this->primaryKey);
        if($LastCode) $LastNum = intval(mb_substr($LastCode,$PrefixLength));
        return ($CodePrefixChar . (str_pad(++$LastNum,$NumberLength,"0",STR_PAD_LEFT)));
    }

    public $storage_disk = 'ppo';
    public $storage_path = '';

    public function _validation(){
        $Rule = [
            'code'			=>	'nullable|unique:public_print_objects,code',
            'name'		    =>	'required',
        ];
        $Message = [
            'code.unique'				=>	'The code is already in use.',
            'name.required'	            =>	'Name is mandatory field.',
        ];
        return ['rules' => $Rule, 'messages' => $Message];
    }

    public function scopeWeb($Q){
        $Q->where('web','Yes');
    }

    public function create_new($name, $description = null, $code = null){
        $created_by = ($this->_GETAUTHUSER()) ? $this->_GETAUTHUSER()->partner : null;
        return $this->create(compact('code','name','description','created_by'));
    }

    public function set_file($file){
        $this->storage_path = $this->code;
        $file = $this->upload_file($file);
        $this->update_data(compact('file'));
    }

    public function set_preview($file){
        $this->storage_path = $this->code;
        $preview = $this->upload_file($file);
        $this->update_data(compact('preview'));
    }

    public function upload_file($file){
        $Disk = $this->storage_disk; $Path = $this->storage_path;
        $extension = $file->extension()?:mb_substr(mb_strrchr($file->getClientOriginalName(),'.'),1);
        $Name = mb_strstr($file->hashName(),".",true);
        $File = $Name . '.' . $extension;
        return $file->storeAs($Path,$File,$Disk);
    }

    public function update_data($array){
        $this->update($array);
    }
    public function add_spec($name,$value){
        $this->specs()->updateOrCreate(['print_object'=>$this->code],[$name=>$value]);
    }

    public function createdBy(){
        return $this->belongsTo('App\Models\Partner','created_by','code');
    }
    public function specs(){
        return $this->hasOne('App\Models\PublicPrintObjectSpecs','print_object','code');
    }

}
