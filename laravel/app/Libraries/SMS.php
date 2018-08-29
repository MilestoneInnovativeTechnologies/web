<?php

namespace App\Libraries;


class SMS
{
	
	private $RolesToBeProcessed = ['customer' => 'is_customer_restricted','distributor' => 'is_distributor_restricted','supportagent' => 'should_restrict','supportteam' => 'should_restrict'];
	private $to = "";
	private $txt = "";
	private $gateway = "";
	private $sms = null;
	private $name = null;
	
	public $response = '';
	
	public function __construct($Sms){
		$this->txt = $Sms->message;
		return $this;
	}
	
	public static function init($Sms){
		return new static($Sms);
	}

	public function sms($arg, $role = null){
		$this->start_process($arg, $role);
		return $this;
	}
	
	public function send($arg = null, $role = null){
		if(!is_null($arg)) $this->sms($arg, $role);
		if(!$this->prepare()) return false;
		$this->pre_send_activities();
		$this->transfer();
		$this->post_send_activities();
		return $this->response;
	}

	public function sendTo($num){
        $this->add_number($num);
		if(!$this->prepare()) return false;
		$this->pre_send_activities();
		$this->transfer();
		$this->post_send_activities();
		return $this->response;
	}

	public function gateway($gateway = null){
		if(is_null($gateway)) return $this;
		$this->add_gateway($gateway);
		return $this;
	}

//==============================================================
	
	private function start_process($arg, $role){
		if($role) $this->do_with_role($role, $arg);
		else $this->do_with_arg($arg);
	}
	
	private function do_with_arg($arg){
		$role = $this->get_role($arg);
		if(!$role || !$this->is_processable_role($role)) return $this->prepare_gateway($this->add_phone_from_arg($arg));
		$this->check_restriction($arg, $role);
	}
	
	private function get_role($arg){
		$code = $this->get_code_from_arg($arg); if(!$code) return null;
		return $this->get_role_from_code($code);
	}
	
	private function get_role_from_code($code){
		return $this->get_required_role($this->get_roles_from_code($code));
	}
	
	private function get_required_role($roles){
		if(!$roles || empty($roles)) return null;
		if(count($roles) == 1) return $roles[0];
		if(in_array('supportteam',$roles)) return 'supportteam';
		if(in_array('supportagent',$roles)) return 'supportagent';
		if(in_array('distributor',$roles)) return 'distributor';
		if(in_array('customer',$roles)) return 'customer';
		return null;
	}
	
	private function get_code_from_arg($arg){
		if(is_string($arg)) return $arg;
		if(is_object($arg)) return $arg->code;
		return null;
	}
	
	private function get_obj_from_code($code){
		return \App\Models\Partner::find($code)->load('Details');
	}
	
	private function do_with_role($role, $arg){
		$this->check_restriction($arg, $role);
	}
	
	private function is_processable_role($role){
		if(!$role) return false;
		return array_key_exists($role,$this->RolesToBeProcessed);
	}
	
	private function get_roles_from_code($code){
		return \App\Models\PartnerRole::wherePartner($code)->pluck('rolename')->toArray();
	}
	

//=========================================================
	
	private function add_phone_from_arg($arg){
		$code = $this->get_code_from_arg($arg); if(!$code) return false;
		return $this->add_phone_from_code($code);
	}
	
	private function add_phone_from_code($code){
		$obj = $this->get_obj_from_code($code); if(!$obj) return false;
		return $this->add_phone_from_obj($obj);
	}

	private function add_phone_from_obj($obj){
		$num = $this->get_number_from_obj($obj); if(!$num) return false;
		$this->name = $this->get_name_from_obj($obj);
		return $this->add_number($num);
	}
	
	private function get_number_from_obj($obj){
		$D = $obj->Details;
		$C = ($D->phonecode) ? str_replace("+","0",$D->phonecode) : "";
		$P = ($D->phone) ?  str_replace(" ","",$D->phone) : "";
		return $C.$P;
	}
	
	private function get_name_from_obj($obj){
		return $obj->name;
	}
	
	private function add_number($num){
		$this->to = $num; return true;
	}

//=========================================================
	
	private function check_restriction($arg, $role){
		$code = $this->get_code_from_arg($arg); if(!$code) return null;
		$is_restricted = $this->is_restricted($code, $role);
		if($is_restricted) return;
		$this->prepare_gateway($this->add_phone_from_arg($arg));
	}
	
	private function is_restricted($code, $role){
		if(array_key_exists($role,$this->RolesToBeProcessed)) return $this->{$this->RolesToBeProcessed[$role]}($code);
		return false;
		if($role == 'distributor') return $this->is_distributor_restricted($code);
		if($role == 'customer') return $this->is_customer_restricted($code);
	}

//=========================================================
	
	private function is_distributor_restricted($code){
		$Data = \App\Models\DistributorContactMethod::whereDistributor($code)->first();
		if(!$Data || is_null($Data->sms)) return true;
		$this->add_gateway($Data->sms); return false;
	}
	
	private function is_customer_restricted($code){
		$Data = \App\Models\DistributorCustomerContactMethod::whereCustomer($code)->first();
		if(!$Data) return $this->is_customer_restricted_by_distributor($code);
		if(is_null($Data->sms)) return true;
		$this->add_gateway($Data->sms); return false;
	}
	
	private function is_customer_restricted_by_distributor($code){
		$Data = \App\Models\Customer::find($code); if(!$Data) return true;
		$Distributor = $Data->get_distributor(); $distributor = $Distributor->code;
		return $this->is_distributor_restricted_customer($distributor);
	}
	
	private function is_distributor_restricted_customer($distributor){
		$Data = \App\Models\DistributorCustomerContactMethod::whereDistributor($distributor)->whereNull('customer')->first();
		if(!$Data || is_null($Data->sms)) return true;
		$this->add_gateway($Data->sms); return false;
	}
	
	private function should_restrict(){
		return true;
	}

//=========================================================
	
	private function add_gateway($gateway){
		$this->gateway = $gateway;
	}

//=========================================================
	
	private function prepare_gateway($num){
		if($num !== true || $this->to == "" || $this->txt == "")  return;
		$this->set_gateway($this->gateway);
	}
	
	private function set_gateway($gateway){
		$obj = $this->get_gateway_obj($gateway);
		if(!$obj) return;
		$this->setup_gateway($obj->class, $obj->url, $obj->arg1, $obj->arg2, $obj->arg3, $obj->arg4, $obj->arg5, $obj->arg6, $obj->arg7, $obj->arg8, $obj->arg9);
	}
	
	private function get_gateway_obj($code){
		return \App\Models\SMSGateway::find($code);
	}
	
	private function setup_gateway($class, $url, ...$args){
		$argArray = array_combine(array_map(function($n){ return 'arg'.$n;},range(1,9)),$args);
		$fqc = "\\App\\Libraries\\SMSGateways\\" . $class;
		$this->sms = (new $fqc($this->to, $this->txt))->url($url)->args($argArray);
	}

//==============================================================
	
	private function prepare(){
		return !is_null($this->sms);
	}
	
	private function pre_send_activities(){
		
	}
	
	private function transfer(){
		$this->response = $this->sms->send();
	}
	
	private function post_send_activities(){
		if($this->response !== false) event(new \App\Events\LogSentSMS($this->name, $this->to, $this->txt));
	}
	
}
