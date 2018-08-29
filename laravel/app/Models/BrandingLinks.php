<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandingLinks extends Model
{
	protected $table = 'branding_links';
	protected $primaryKey = 'id';
	public $incrementing = true;
	public $timestamps = true;
	//protected $fillable = ['code'];
	protected $guarded = [];
	protected $hidden = ['created_at','updated_at'];
}
