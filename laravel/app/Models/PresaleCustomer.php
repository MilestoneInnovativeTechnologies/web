<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresaleCustomer extends Model
{

	public $incrementing = false;
	protected $primaryKey = 'customer';
	protected $fillable = array('presale_enddate', 'presale_extended_to', 'presale_extended_by');
	protected $table = "customer_registrations";

	
}
