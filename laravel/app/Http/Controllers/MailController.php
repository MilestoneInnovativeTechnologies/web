<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MailController extends Controller
{
	
	
	protected $Model;
	public $replaceable = ['[_NAME_]' => 'replace_message_name','[_EMAIL_]' => 'replace_message_email','[_CODE_]' => 'replace_message_code'];
	
	public function __construct(){
		$Apply = ['compose','update','send'];
		$this->middleware(function($request, $next){
			$segs = $request->segments(); switch (count($segs)){ case 2: list($prefix,$action) = $segs; break; case 3: list($prefix,$code,$action) = $segs; break; default: list($prefix) = $segs; break; }
			$this->Model = $Model = (isset($code)) ? \App\Models\Mail::find($code) : new \App\Models\Mail;
			return (in_array($action,$Model->available_actions)) ? $next($request) : redirect()->back()->with(['info'	=>	true, 'type'	=>	'warning', 'text'	=>	'The requested action is not available right now.']);
		})->only($Apply);
	}
	
	public function compose(Request $request){
		$val_rules = $this->Model->_validation();
		$validator = \Validator::make($request->all(),$val_rules['rules'],$val_rules['messages']);
		if($validator->fails()) return redirect()->back()->withErrors($validator)->withInput();
		call_user_func_array([$this->Model,'create_new'],$request->only('code','subject','body'));
		return redirect()->route('mail.index')->with(['info' => true, 'type' => 'success', 'text' => 'New Email Message created successfully.']);
	}
	
	public function update(Request $request){
		$val_rules = $this->Model->_validation();
		if($this->Model->code == $request->code) unset($val_rules['rules']['code']);
		$validator = \Validator::make($request->all(),$val_rules['rules'],$val_rules['messages']);
		if($validator->fails()) return redirect()->back()->withErrors($validator)->withInput();
		foreach($request->only('code','subject','body') as $field => $req_value){
			if($this->Model->$field != $req_value)
				$this->Model->$field = $req_value;
		}
		$this->Model->save();
		return redirect()->back()->with(['info' => true, 'type' => 'success', 'text' => 'Email Message updated successfully.']);
	}
	
	public function send(Request $request){
		if(!$request->r || empty($request->r)) return redirect()->back()->with(['info' => true, 'type' => 'warning', 'text' => 'No receipts found to send E-Mail.']);
		foreach($request->r as $r){
			$message = $this->replace_message_items($this->Model->body,$r);
			$message = $this->make_message_trackabe($this->Model->code,$r,$message);
			$r_obj = (filter_var($r,FILTER_VALIDATE_EMAIL))?:$this->code_to_obj($r);
			$Status = \App\Libraries\Mail::init($message,$this->Model->subject)->send($r_obj);
			if($Status !== false) $this->log_sent($this->Model->code,$r_obj,$this->Model->subject);
		}
		return redirect()->route('mail.index')->with(['info' => true, 'type' => 'success', 'text' => 'Email Queued Successfully.']);
	}
	
	public function replace_message_items($message,$receiver){
		if(filter_var($receiver,FILTER_VALIDATE_EMAIL) == $receiver) $message = $this->replace_text('[_EMAIL_]',$receiver,$message);
		foreach($this->replaceable as $search => $method){
			if(mb_stripos($message,$search) !== false) $message = $this->{$method}($message,$receiver);
		}
		return $message;
	}
	
	public function replace_text($search, $replace, $message){
		return str_replace($search,$replace,$message);
	}
	
	public function make_message_trackabe($code,$receiver,$message){
		$receiver = (filter_var($receiver,FILTER_VALIDATE_EMAIL))?:$this->code_to_name($receiver);
		$key = $this->get_mail_tracking_code($code,$receiver); $url = $this->get_track_url($key);
		$html = $this->get_tracking_html($url);
		return $this->append_tracking_html($message,$html);
	}
	
	private function log_sent($code,$receiver,$subject = null){
		$wml = new \App\Http\Controllers\WebMailLogController;
		$receiver = is_object($receiver) ? $receiver->name : $receiver;
		$wml->new_sent($code,$receiver,$subject);
	}
	
	private function replace_message_name($message, $code){
		$Obj = \App\Models\Partner::find($code); if(!$Obj) return $this->replace_text('[_NAME_]','',$message);
		$name = $Obj->name; return $this->replace_text('[_NAME_]',$name,$message);
	}
	
	private function replace_message_email($message, $code){
		$Obj = \App\Models\PartnerLogin::wherePartner($code)->first(); if(!$Obj) return $this->replace_text('[_EMAIL_]','',$message);
		$email = $Obj->email; return $this->replace_text('[_EMAIL_]',$email,$message);
	}
	
	private function replace_message_code($message, $code){
		return $this->replace_text('[_CODE_]',$code,$message);
	}
	
	private function code_to_obj($code){
		return \App\Models\Partner::find($code);
	}
	
	private function code_to_name($code){
		$Obj = $this->code_to_obj($code); if(!$Obj) return '';
		return $Obj->name;
	}
	
	private function get_mail_tracking_code($code, $receiver){
		return \App\Http\Controllers\KeyCodeController::Encode(['mail','receiver'], [$code,$receiver]);
	}
	
	private function get_track_url($key){
		return route('webmail.track',$key);
	}
	
	private function get_tracking_html($url){
		$company = \App\Models\Company::first();
		$home_url = route('home');
		$HTML = <<<HTML
<br><br><br>
<br><br><br>
<br><hr style='color:#ddd'>
<span style="color:#ddd; font-size:12px"><a href='$home_url' target='_blank' style='font-size:inherit; color:#ddd'>$company->name</a> | $company->email | $company->phone</span>
<img src="$url" style="float:right; height:26px; width:66px;">
HTML;
		return $HTML;
	}
	
	private function append_tracking_html($message,$html){
		return $message .= $html;
	}
	
	public function log(Request $request){
		return (new \App\Http\Controllers\WebMailLogController)->webmail($request->code);
	}
	
	
}
