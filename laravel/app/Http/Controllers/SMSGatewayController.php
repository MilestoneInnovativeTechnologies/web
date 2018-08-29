<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SMSGateway as Gateway;

class SMSGatewayController extends Controller
{
	
	protected $Gateway;
	public function __construct(){
		$Apply = ['store','update','inactivate'];
		$this->middleware(function($request, $next){
			$segs = $request->segments(); switch (count($segs)){ case 2: list($prefix,$action) = $segs; break; case 3: list($prefix,$code,$action) = $segs; break; default: list($prefix) = $segs; break; }
			$this->Gateway = $Gateway = (isset($code)) ? Gateway::find($code) : new Gateway;
			return (in_array($action,$Gateway->available_actions)) ? $next($request) : redirect()->back()->with(['info'	=>	true, 'type'	=>	'warning', 'text'	=>	'The requested action is not available right now.']);
		})->only($Apply);
	}
	
	public function store(Request $request){
		$val_rules = $this->Gateway->_validation();
		$validator = \Validator::make($request->all(),$val_rules['rules'],$val_rules['messages']);
		if($validator->fails()) return redirect()->back()->withErrors($validator)->withInput();
		call_user_func_array([$this->Gateway,'create_new'],$request->only($this->Gateway->fillable_fields()));
		return redirect()->back()->with(['info' => true, 'type' => 'success', 'text' => 'New SMS Gateway added successfully.']);
	}
	
	public function update(Request $request){
		$val_rules = $this->Gateway->_validation();
		if($this->Gateway->code == $request->code) unset($val_rules['rules']['code']);
		$validator = \Validator::make($request->all(),$val_rules['rules'],$val_rules['messages']);
		if($validator->fails()) return redirect()->back()->withErrors($validator)->withInput();
		foreach($request->only($this->Gateway->fillable_fields()) as $field => $req_value){
			if($this->Gateway->$field != $req_value)
				$this->Gateway->$field = $req_value;
		}
		$this->Gateway->save();
		return redirect()->back()->with(['info' => true, 'type' => 'success', 'text' => 'SMS Gateway details updated successfully.']);
	}
	
	public function inactivate(){
		//return $this->Gateway;
		$this->Gateway->status = 'INACTIVE'; $this->Gateway->save();
		return redirect()->route('smsg.index')->with(['info' => true, 'type' => 'success', 'text' => 'The SMS Gateway '.$this->Gateway->name.' made as INACTIVE.']);
	}




}
