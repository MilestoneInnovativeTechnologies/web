<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerProduct extends Model
{

	protected $primaryKey = 'customer';
	protected $fillable = array('customer', 'product', 'edition', 'seqno', 'serialno', 'using_version', 'lastused_on', 'downloaded_version', 'downloaded_on');
	public $timestamps = false;
	public $incrementing = false;

}
