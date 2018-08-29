<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatabaseBackup extends Model
{
	protected $table = 'database_backups';
	protected $primaryKey = 'id';
	public $incrementing = true;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['created_at','updated_at','file'];
	//protected $visible = ['code','name','Details','Logins'/*,'Roles'*/,'Privilage','Defaultst','Customers','Distributors'];
	protected $with = ['Customer','User'];
	
	public $backup_disk = 'local';
	public $backup_folder = 'Backup/Database';
	public $within_limit = '1';
	
	protected $appends = ['download_link'];
	
	public function getDownloadLinkAttribute($value = null){
		return Route('backup.database.download',\App\Http\Controllers\KeyCodeController::Encode(['customer','link','id','name'],[$this->Customer->code,$this->file,$this->id,$this->Customer->name]));
	}
	
	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('own', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$user = (\Auth()->user()) ?: Auth()->guard('api')->user(); if($user){
				$builder->where(function($Q){ $Q->has('Customer'); });
			}
		});
		static::addGlobalScope('active', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$builder->where(function($Q){ $Q->where('status','<>','INACTIVE'); });
		});
		static::addGlobalScope('latest', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$builder->latest('id');
		});
		static::addGlobalScope('limit', function (\Illuminate\Database\Eloquent\Builder $builder) {
			$builder->take(1);
		});
	}

	public function user(){
		return $this->belongsTo('App\Models\Partner','user','code')->select('code','name');
	}
	
	public function customer(){
		return $this->belongsTo('App\Models\Customer','customer','code')->select('code','name');
	}

	
	
	public function get_backup_folder($customer = null){
		return join("/",[$this->backup_folder,$customer]);
	}

	
	public function add_new($customer, $file, $user, $details = null, $size = null, $mime = null, $format = null, $status = 'WITHIN'){
		$size = ($size)?:$this->get_backup_size($file); $mime = ($mime)?:$this->get_backup_mime($file); $format = ($format)?:$this->get_backup_ext($file); $status = ($status)?:'WITHIN';
		$data = $this->create(compact('customer','details','file','mime','format','size','user','status'));
		$this->limit_active($customer);
		return $data;
	}
	
	public function store_backup($customer, $file){
		$Path = $this->get_backup_folder($customer);
		if($file->extension()) return $file->store($Path,$this->backup_disk);
		$ext = mb_strrchr($file->getClientOriginalName(),'.');
		$filename = $file->hashName(); if(mb_substr($filename,-1) == ".") $filename = mb_substr($filename,0,-1);
		return $file->storeAs($Path,$filename.$ext,$this->backup_disk);
	}
	
	public function get_backup_size($path){
		return \Storage::disk($this->backup_disk)->size($path);
	}
	
	public function get_backup_mime($path){
		return \Storage::disk($this->backup_disk)->mimeType($path);
	}
	
	public function get_backup_ext($path){
		return mb_substr(mb_strrchr($path,"."),1);
	}
	
	public function limit_active($customer){
		$limit = $this->within_limit;
		$this->where(['customer' => $customer, 'status' => 'WITHIN'])->skip($limit)->take(200)->get()->each(function($item){ $item->update(['status' => 'OUTSIDE']); });
	}
	
	public function scopeLimit($Q){
		return $Q->take($this->within_limit);
	}
	
	public function upload_validations(){
		$Rules = [
			'customer'	=> 'bail|required|exists:partners,code',
			'backup'	=> 'required|file',
		];
		$Messages = [
			'customer.required'	=> 'Please enter a proper customer.',
			'customer.exists'	=> 'Customer doesn\'t exists.',
			'backup.required'	=> 'Backup file missing.',
			'backup.file'	=> 'Backup file is invalid.',
		];
		return ['rules' => $Rules, 'messages' => $Messages];
	}
}