<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libraries\Mail;

class Test extends Controller
{
	
	public function index(){
		dd(Mail::init()->to('CUST000021')->to('CUST000013')->cc('CUST000028')->bcc('CUST000017')->bcc('CUST000029')->queue(new \App\Mail\MailTest)->send());
	}
	
	
	
	
}
