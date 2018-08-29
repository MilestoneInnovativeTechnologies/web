<?php

namespace App\Libraries\SMSGateways;


class SmppSMSHub
{
	
	private $curl = null;
	
	protected $To = null;
	protected $Message = null;
	
	public $Response = null;
	
	public function __construct($To,$Message){
		
		$this->To = $To;
		$this->Message = $Message;
		
	}
	
	public static function init($To,$Message){
		return new self($To,$Message);
	}
	
	protected $Arguments = ['arg1' => 'user', 'arg2' => 'password', 'arg3' => 'senderid', 'arg4' => 'channel', 'arg5' => 'DCS', 'arg6' => 'flashsms', 'arg7' => 'route'];
	protected $Parameters = ['user' => 'user', 'password' => 'password', 'senderid' => 'senderid', 'channel' => 'channel', 'DCS' => 'DCS', 'flashsms' => 'flashsms', 'route' => 'route', 'number' => 'To', 'text' => 'Message'];
	
	private $url = 'http://smppsmshub.in/api/mt/SendSMS';
	private $user = 'milestoneit';
	private $password = 'pass123!';
	private $senderid = 'MLSTNE';
	private $channel = 'Trans';
	private $route = '32';
	private $DCS = '0';
	private $flashsms = '0';
	
	public function url($url){
		$this->url = $url;
		return $this;
	}
	
	public function args($Ary){
		$this->SetArrayToArgs($Ary);
		return $this;
	}

	public function send(){
		if(!$this->correct_number()) return false;
		if(!$this->correct_message()) return false;
		$this->prepare();
		$this->execute();
		return $this->Response;
	}
	
	
	//============================================================================
	
	
	private function SetArrayToArgs($Ary){
		foreach($this->Arguments as $name => $para){
			if(array_key_exists($name,$Ary) && !is_null($Ary[$name]))
				$this->$para = $Ary[$name];
		}
	}
	
	public function correct_number(){
		$num = $this->To;
		$num = str_replace(" ","",str_replace("+","00",$num));
		if(strlen($num) < 10) return false;
		if(strlen($num)>12 && intval(mb_substr($num,0,strlen($num)-12)) == 0) $num = mb_substr($num,-12);
		if(strlen($num) == 10) $num = "91" . $num;
		$this->To = $num;
		return (strlen($num) == 12 && intval(mb_substr($num,0,2)) == 91);
	}
	
	public function correct_message(){
		if(is_null($this->Message) || empty($this->Message) || trim($this->Message) == "") return false;
		$this->trim_message(155);
		return true;
	}
	
	private function trim_message($length){
		$this->Message = mb_substr($this->Message,0,$length);
	}

	private function prepare(){
		$url = $this->get_geturl();
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$this->curl = $curl;
	}

	private function PostFields(){
		$PostFields = [];
		foreach($this->Parameters as $Param => $Var)
			$PostFields[$Param] = $this->$Var;
		return $PostFields;
	}
	
	private function array_to_query($ary){
		return http_build_query($ary);
	}
	
	private function execute(){
		$this->Response = curl_exec($this->curl);
		curl_close($this->curl);
	}
	
	private function get_geturl(){
		$url = $this->url; $qAry = $this->PostFields();
		$params = $this->array_to_query($qAry);
		return $url . "?" . $params;
	}
	
}