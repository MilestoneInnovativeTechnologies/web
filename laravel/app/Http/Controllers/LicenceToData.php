<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LicenceToData extends Controller
{
    //
	
	public function upload(Request $Request){
		//return $_FILES;
		$xml = simplexml_load_string(file_get_contents($Request->licence->path()));
		return json_encode($xml);
	}
}
