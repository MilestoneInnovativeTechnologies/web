<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;

use App\Mail\test;
use App\Models\Partner;

class MailTest extends Controller
{
    //
	public function send(){
		
		$Partner = 'CUST000006';
		//return $this->getPartnerDetails($Partner,true);
		dd( Mail::queue(new test('CUST000008')));
	}

}
