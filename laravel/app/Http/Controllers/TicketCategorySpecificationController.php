<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TicketCategorySpecification as TCS;

class TicketCategorySpecificationController extends Controller
{
	
	protected $tcs;
	
	public function __construct(){
		$Apply = ['add','edit','delete','activate'];
		$this->middleware(function($request, $next){
			$segs = $request->segments(); switch (count($segs)){ case 2: list($prefix,$action) = $segs; break; case 3: list($prefix,$tcs,$action) = $segs; break; default: list($prefix) = $segs; break; }
			$this->tcs = $tcs = (isset($tcs)) ? TCS::withoutGlobalScopes()->find($tcs) : new TCS;
			return (in_array($action,$tcs->available_actions)) ? $next($request) : redirect()->back()->with(['info'	=>	true, 'type'	=>	'warning', 'text'	=>	'The requested action is not available right now.']);
		})->only($Apply);
	}
	
	public function add(Request $request){
		$ValidationRules = $this->tcs->validation_rules(); $Rules = $ValidationRules['rules']; $Messages = $ValidationRules['messages'];
		$Validation = \Validator::make($request->all(),$Rules,$Messages);
		if($Validation->fails()) return redirect()->back()->withErrors($Validation)->withInput();
		$this->tcs->add_new($request->code, $request->name, $request->type, $request->description, $request->spec);
		return redirect()->back()->with(['info'	=>	true, 'type'	=>	'success', 'text'	=>	'New Specification has been added successfully.']);
	}
	
	public function edit(Request $request){
		$tcs = $this->tcs; $ValidationRules = $tcs->validation_rules(); $Rules = $ValidationRules['rules']; $Messages = $ValidationRules['messages'];
		$Rules['code'] = ['nullable',\Illuminate\Validation\Rule::unique('ticket_category_specifications')->ignore($tcs->code,'code')];
		$Validation = \Validator::make($request->all(),$Rules,$Messages);
		if($Validation->fails()) return redirect()->back()->withErrors($Validation)->withInput();
		$tcs->update($request->only('code','name','description','type','spec'));
		return redirect()->route('tcs.edit',$tcs->code)->with(['info'	=>	true, 'type'	=>	'success', 'text'	=>	'Specification updated successfully.']);
	}
	
	public function delete(){
		$this->tcs->del();
		return redirect()->route('tcs.index')->with(['info'	=>	true, 'type'	=>	'warning', 'text'	=>	'Specification deleted successfully.']);
	}
	
	public function activate(){
		$this->tcs->activate();
		return redirect()->back()->with(['info'	=>	true, 'type'	=>	'success', 'text'	=>	$this->tcs->name . ' made as ACTIVE.']);
	}
	
}
