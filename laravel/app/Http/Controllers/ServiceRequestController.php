<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceRequest as SR;

class ServiceRequestController extends Controller
{
	
	protected $sr;
	
	public function __construct(){
		$Apply = ['add','edit','delete','respond','response'];
		$this->middleware(function($request, $next){
			$segs = $request->segments(); switch (count($segs)){ case 2: list($prefix,$action) = $segs; break; case 3: list($prefix,$sr,$action) = $segs; break; default: list($prefix) = $segs; break; }
			$this->sr = $sr = (isset($sr)) ? SR::find($sr) : new SR;
			return (in_array($action,$sr->available_actions)) ? $next($request) : redirect()->back()->with(['info'	=>	true, 'type'	=>	'warning', 'text'	=>	'The requested action is not available right now.']);
		})->only($Apply);

	}
	
	public function index(){
		return view('sreq.index');
	}
	
	public function edit(Request $request){
		$sr = $this->sr; $val_rules = $sr->validation_rules();
		$validator = \Validator::make($request->all(),$val_rules['rules'],$val_rules['messages']);
		if($validator->fails()) return redirect()->back()->withErrors($validator)->withInput();
		$sr->supportteam = $request->supportteam; $sr->message = $request->message; $sr->save();
		return redirect()->route('sreq.index')->with(['info' => true, 'type' => 'success', 'text' => 'Service Request updated successfully.']);
	}
	
	public function delete(){
		$this->sr->delete();
		return redirect()->route('sreq.index')->with(['info' => true, 'type' => 'warning', 'text' => 'Service Request deleted successfully.']);
	}
	
	public function add(Request $request){
		$sr = $this->sr; $val_rules = $sr->validation_rules();
		$validator = \Validator::make($request->all(),$val_rules['rules'],$val_rules['messages']);
		if($validator->fails()) return redirect()->back()->withErrors($validator)->withInput();
		$sr = $sr->store($request->supportteam,$request->message,$this->getAuthUser()->partner);
		$this->SendMail('SREQCreated',$sr,$request->supportteam);
		return redirect()->route('sreq.index')->with(['info' => true, 'type' => 'success', 'text' => 'Service Request added successfully.']);
	}
	
	public function respond(Request $request){
		if(!$request->response) return redirect()->back()->with(['info' => true,'type' => 'danger','text' => 'Response field cannot be empty, Please enter response in detail.']);
		$user = $this->getAuthUser();
		$this->sr->add_response($request->response, $user->partner, $user->Roles->implode('displayname',', '));
		return redirect()->route('sreq.index')->with(['info' => true, 'type' => 'success', 'text' => 'Response added successfully.']);
	}
	
	public function response(Request $request){
		if(!$request->response) return redirect()->back()->with(['info' => true,'type' => 'danger','text' => 'Response field cannot be empty, Please enter response in detail.']);
		$user = $this->getAuthUser();
		$this->sr->add_response($request->response, $user->partner, $user->Roles->implode('displayname',', '));
		return redirect()->route('sreq.index')->with(['info' => true, 'type' => 'success', 'text' => 'Response updated successfully.']);
	}

	private function getAuthUser(){
		return (Auth()->user())?:Auth()->guard('api')->user();
	}
	
	private function SendMail($Mail,$Object,$To){
		$Class = '\\App\\Mail\\' . $Mail;
		\App\Libraries\Mail::init()->queue(new $Class($Object))->send($To);
	}
}
