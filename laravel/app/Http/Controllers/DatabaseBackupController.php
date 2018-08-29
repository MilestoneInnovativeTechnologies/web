<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DatabaseBackup as dbb;

class DatabaseBackupController extends Controller
{
	
	
	public function index(){
		return view('dbb.index');
	}
	
	public function upload(Request $request){
		$dbb = new dbb; $rules = $dbb->upload_validations();
		if(session()->get('_rolename') == 'customer') $request->merge(['customer' => $this->getAuthUser()->partner]);
		$validator = \Validator::make($request->all(),$rules['rules'],$rules['messages']); if($validator->fails()) return redirect()->back()->withErrors($validator)->withInput();
		$path = $dbb->store_backup($request->customer, $request->backup);
		$dbb->add_new($request->customer, $path, $this->getAuthUser()->partner, $request->details);
		return redirect()->back()/*route('dbb.index')*/->with(['info' => true, 'type' => 'success', 'text' => 'Backup uploaded successfully.']);
	}
	
	
	private function getAuthUser(){
		return (Auth()->user())?:Auth()->guard('api')->user();
	}
	
}
