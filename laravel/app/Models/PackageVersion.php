<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageVersion extends Model
{
	public $incrementing = false;
	protected $primaryKey = 'product';
	protected $fillable = array('product', 'edition', 'package', 'version_sequence', 'major_version','minor_version','build_version','revision','version_string','version_numeric','build_date','deploy_date','approved_date','change_log','bugs','file','status','status_reason', "created_by");
	
	public function product(){
		return $this->belongsTo('App\Models\Product','product','code')->whereActive("1");
	}
	public function edition(){
		return $this->belongsTo('App\Models\Edition','edition','code')->whereActive("1");
	}
	public function package(){
		return $this->belongsTo('App\Models\Package','package','code')->whereActive("1");
	}
	
	public function scopeStatus($Q,$Status){
		return $Q->whereStatus($Status);
	}
	public function scopeAwaiting($Q){
		return $this->scopeStatus($Q,"AWAITING UPLOAD");
	}
	public function scopePending($Q){
		return $this->scopeStatus($Q,"PENDING");
	}
}
