<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DistributorBranding as DB;

class BrandController extends Controller
{
	
	
	public function index(){
		//$host = Request()->server('HTTP_HOST');
		//return $host;
		$Data = DB::whereDomain('milestoneeplus.com')->with('Branding')->first();
		return view('brand.index',compact('Data'));
	}
	
	
	
	
	
	
	
	
	
	
	
}
