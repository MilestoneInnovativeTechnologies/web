<?php

namespace App\Libraries;

use Mail as LaravelMail;

class Mail extends LaravelMail
{
	
	private $RolesToBeProcessed = ['customer','distributor'];
	private $Raw = null;
	private $Subject = null;
	private $Mailable = null;
	private $toAddress = null;
	private $ccAddress = null;
	private $bccAddress = null;
	private $addTo = 'to';
	private $key = null;
	
	public function __construct($raw = null, $subject = null){
		$this->Raw = $raw; $this->Subject = $subject;
		return $this;
	}
	
	public static function init($raw = null, $subject = null){
		return new static($raw, $subject);
	}
	
	public function send($arg = null, $role = null){
		if(!is_null($arg)) $this->to($arg, $role);
		$this->pre_send_activities();
		$response = $this->transport();
		$this->post_send_activities();
		return $response;
	}

	public function queue($Mailable){
		$this->Mailable = $Mailable;
		return $this;
	}
	
	public function to($arg = null, $role = null){
		if(is_null($arg)) return $this; $this->addTo = 'to';
		$this->start_process($arg, $role);
		return $this;
	}
	
	public function cc($arg = null, $role = null){
		if(is_null($arg)) return $this; $this->addTo = 'cc';
		$this->start_process($arg, $role);
		return $this;
	}
	
	public function bcc($arg = null, $role = null){
		if(is_null($arg)) return $this; $this->addTo = 'bcc';
		$this->start_process($arg, $role);
		return $this;
	}
	
	public function key($key = null){
		$this->key = $key;
		return $this;
	}
	
//==============================================================
	
	private function start_process($arg, $role){
		if($role) $this->do_with_role($role, $arg);
		else $this->do_with_arg($arg);
	}
	
	private function do_with_arg($arg){
		$role = $this->get_role($arg);
		if(!$role || !$this->is_processable_role($role)) return $this->add_address_from_arg($arg, $this->addTo);
		$this->check_restriction($arg, $role);
	}
	
	private function do_with_role($role, $arg){
		$this->check_restriction($arg, $role);
	}
	
	private function is_processable_role($role){
		if(!$role) return false;
		return in_array($role,$this->RolesToBeProcessed);
	}

	private function is_email($arg){
		return (filter_var($arg,FILTER_VALIDATE_EMAIL) == $arg);
	}
	
	private function get_role($arg){
		if(is_string($arg)){
			if($this->is_email($arg)) $role = $this->get_role_from_email($arg);
			else $role = $this->get_role_from_code($arg);
		} else $role = $this->get_role_from_obj($arg);
		return $role;
	}
	
	private function get_role_from_email($email){
		$code = $this->get_code_from_email($email);
		if(!$code) return false;
		return $this->get_role_from_code($code);
	}
	
	private function get_role_from_obj($obj){
		return $this->get_required_role($this->get_roles_array_from_obj($obj));
	}
	
	private function get_role_from_code($code){
		return $this->get_required_role($this->get_roles_from_code($code));
	}
	
	private function get_required_role($roles){
		if(!$roles || empty($roles)) return null;
		if(in_array('distributor',$roles)) return 'distributor';
		if(in_array('customer',$roles)) return 'customer';
		return null;
	}
	
	private function get_roles_array_from_obj($obj){
		if(!$obj) return [];
		$Roles = $obj->load('Roles')->Roles; if(!$Roles || is_null($Roles) || $Roles->isEmpty()) return [];
		return (is_null($Roles[0]->rolename)) ? $Roles->pluck('name')->toArray() : $Roles->pluck('rolename')->toArray();
	}
	
	private function get_code_from_email($email){
		$Data = \App\Models\PartnerLogin::whereEmail($email)->first();
		if($Data) return $Data->partner; return null;
	}
	
	private function get_roles_from_code($code){
		return \App\Models\PartnerRole::wherePartner($code)->pluck('rolename')->toArray();
	}
	
	private function get_code_from_arg($arg){
		if(is_string($arg)){
			if($this->is_email($arg)) $code = $this->get_code_from_email($arg);
			else $code = $arg;
		} else $code = $arg->code;
		return $code;
	}
	
	private function check_restriction($arg, $role){
		$code = $this->get_code_from_arg($arg); if(!$code) return;
		$is_restricted = $this->is_restricted($code, $role);
		if(!$is_restricted) $this->add_address_from_arg($arg, $this->addTo);
	}
	
	private function is_restricted($code, $role){
		if($role == 'distributor') return $this->is_distributor_restricted($code);
		if($role == 'customer') return $this->is_customer_restricted($code);
		return false;
	}
	
	private function is_distributor_restricted($code){
		$Data = \App\Models\DistributorContactMethod::whereDistributor($code)->first();
		return ($Data && $Data->email == 'No');
	}
	
	private function is_customer_restricted($code){
		$Data = \App\Models\DistributorCustomerContactMethod::whereCustomer($code)->first();
		if(!$Data) return $this->is_customer_restricted_by_distributor($code);
		return ($Data->email == 'No');
	}
	
	private function is_customer_restricted_by_distributor($code){
		$Data = \App\Models\Customer::find($code); if(!$Data) return false;
		$Distributor = $Data->get_distributor(); $distributor = $Distributor->code;
		return $this->is_distributor_restricted_customer($distributor);
	}
	
	private function is_distributor_restricted_customer($distributor){
		$Data = \App\Models\DistributorCustomerContactMethod::whereDistributor($distributor)->whereNull('customer')->first();
		if(!$Data) return false;
		return ($Data->email == 'No');
	}

	public static function __callStatic($function, $arguments){
		return (new self)->handle_function($function, $arguments);
	}
	
	public function __call($function, $arguments){
		return $this->handle_function($function, $arguments);
	}
	
	public function handle_function($function, $arguments){
		
		
		
	}
	
	private function get_arg_to_obj($arg){
		if(is_object($arg)) return $this->get_login_obj_from_obj($arg);
		$code = $this->get_code_from_arg($arg);
		if($code) return $this->get_login_obj_from_code($code);
		return null;
	}
	
	private function get_login_obj_from_code($code){
		return \App\Models\Partner::find($code)->load('Logins');
	}
	
	private function get_login_obj_from_obj($obj){
		return ($obj->relationLoaded('Logins')) ? $obj : $obj->load('Logins');
	}
	
	private function add_address_from_arg($arg,$to){
		$obj = $this->get_arg_to_obj($arg);
		if(!$obj && $this->is_email($arg)) return $this->add_to_address(['name' => '', 'email' => $arg],$to);
		if($obj->Logins->isNotEmpty()) return $this->add_to_address(['name' => $obj->name, 'email' => $obj->Logins->first()->email],$to);
	}
	
	private function add_to_address($Array,$to){
		$VAR = $to . 'Address';
		if(is_null($this->$VAR)) $this->$VAR = collect([$Array]);
		else $this->$VAR->push($Array);			
	}
	
//==============================================================
	
	private function prepare(){
		if(is_null($this->Mailable) && (is_null($this->Raw) || is_null($this->Subject))) return false;
		if($this->is_to()) return true;
		if($this->is_cc()) return $this->move_cc_to_toaddress();
		return false;
	}
	
	private function move_cc_to_toaddress(){
		$this->toAddress = $this->ccAddress; $this->ccAddress = null;
		return true;
	}
	
	private function is_to(){ return $this->is_address('to');	}
	private function is_cc(){ return $this->is_address('cc');	}
	private function is_bcc(){ return $this->is_address('bcc');	}
	
	private function is_address($address){
		$VAR = $address . 'Address';
		return (!is_null($this->$VAR) && $this->$VAR->isNotEmpty());
	}
	
//==============================================================
	
	private function transport(){
		if(!$this->prepare()) return false;
		if(!is_null($this->Raw)) return $this->transport_raw();
		else return $this->transport_mailable();
	}
	
	private function transport_mailable(){
		if($this->is_bcc()) {
			if($this->is_cc()) $response = parent::to($this->toAddress)->bcc($this->bccAddress)->cc($this->ccAddress)->queue($this->Mailable);
			else $response = parent::to($this->toAddress)->bcc($this->bccAddress)->queue($this->Mailable);
		} elseif($this->is_cc()) {
			$response = parent::to($this->toAddress)->cc($this->ccAddress)->queue($this->Mailable);
		}	else {
			$response = parent::to($this->toAddress)->queue($this->Mailable);
		}
		return $response;
	}
	
	private function transport_raw(){
		$raw = $this->Raw; $subject = $this->Subject; $to = $this->toAddress->pluck('name','email')->toArray();
		$bcc = ($this->is_bcc()) ? $this->bccAddress->pluck('name','email')->toArray() : null;
		$cc = ($this->is_cc()) ? $this->ccAddress->pluck('name','email')->toArray() : null;
		if($bcc) {
			if($cc) $response = parent::send([],[],function($message)use($raw, $subject, $to, $cc, $bcc){ $message->subject($subject)->to($to)->cc($cc)->bcc($bcc)->setBody($raw,'text/html'); });
			else $response = parent::send([],[],function($message)use($raw, $subject, $to, $bcc){ $message->subject($subject)->to($to)->bcc($bcc)->setBody($raw,'text/html'); });
		} elseif($cc) {
			$response = parent::send([],[],function($message)use($raw, $subject, $to, $cc){ $message->subject($subject)->to($to)->cc($cc)->setBody($raw,'text/html'); });
		}	else {
			$response = parent::send([],[],function($message)use($raw, $subject, $to){ $message->subject($subject)->to($to)->setBody($raw,'text/html'); });
		}
		return $response;
	}
	
	private function pre_send_activities(){
		$this->add_common_bcc();
	}
	
	private function post_send_activities(){
		$this->log_sent_mail();
	}
	
//==============================================================
	
	private function add_common_bcc(){
		$this->add_to_address(['name' => 'Thahir', 'email' => 'thahir@milestoneit.net'],'bcc');
	}
	
	private function log_sent_mail(){
		$Extra = ['Key' => $this->mail_key(),'CC' => $this->ccAddress,'BCC' => $this->bccAddress];
		event(new \App\Events\LogSentMail($this->toAddress,($this->Subject)?:$this->Mailable->subject,($this->Raw)?'RAW MESSAGE':$this->Mailable->view,$Extra));
	}
	
	private function mail_key(){
		return ($this->key)?:md5(date("YmdHis"));
	}
}
