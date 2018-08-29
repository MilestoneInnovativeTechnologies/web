<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationAudience extends Model
{
	
	protected $table = 'notification_audiences';
	protected $primaryKey = 'id';
	public $incrementing = true;
	public $timestamps = false;
	//protected $fillable = ['code','name','description','class','url','arg1','arg2','arg3','arg4','arg5','arg6','arg7','arg8','arg9'];
	protected $guarded = [];
	//protected $hidden = ['created_at','updated_at'];
	//protected $visible = ['code','name','Details','Logins'/*,'Roles'*/,'Privilage','Defaultst','Customers','Distributors'];
	//protected $with = ['Customer','User'];
	
}
