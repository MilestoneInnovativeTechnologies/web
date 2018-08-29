<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicArticleController extends Controller
{
	
	
	protected $Model;
	
	public function __construct(){
		$Apply = ['store','update'];
		$this->middleware(function($request, $next){
			$segs = $request->segments(); switch (count($segs)){ case 2: list($prefix,$action) = $segs; break; case 3: list($prefix,$code,$action) = $segs; break; default: list($prefix) = $segs; break; }
			$this->Model = $Model = (isset($code)) ? \App\Models\PublicArticle::find($code) : new \App\Models\PublicArticle;
			return (in_array($action,$Model->available_actions)) ? $next($request) : redirect()->back()->with(['info'	=>	true, 'type'	=>	'warning', 'text'	=>	'The requested action is not available right now.']);
		})->only($Apply);
	}
	
	public function store(Request $request){
		$val_rules = $this->Model->_validation();
		$validator = \Validator::make($request->all(),$val_rules['rules'],$val_rules['messages']);
		if($validator->fails()) return redirect()->back()->withErrors($validator)->withInput();
		call_user_func_array([$this->Model,'create_new'],$request->only('code','name','title','view'));
		return redirect()->route('pa.index')->with(['info' => true, 'type' => 'success', 'text' => 'Article created successfully.']);
	}
	
	public function update(Request $request){
		$val_rules = $this->Model->_validation();
		if($this->Model->code == $request->code) unset($val_rules['rules']['code']);
		$validator = \Validator::make($request->all(),$val_rules['rules'],$val_rules['messages']);
		if($validator->fails()) return redirect()->back()->withErrors($validator)->withInput();
		foreach($request->only('code','name','title','view') as $field => $req_value){
			if($this->Model->$field != $req_value)
				$this->Model->$field = $req_value;
		}
		$this->Model->save();
		return redirect()->back()->with(['info' => true, 'type' => 'success', 'text' => 'Article updated successfully.']);
	}
	
	public function serve($code){
		$PA = \App\Models\PublicArticle::find($code);
		$PA->increment('count');
		return view('pa.articles.'.$PA->view,['Title' => $PA->title]);
	}
	
}
