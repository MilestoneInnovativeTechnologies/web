<?php

namespace App\Sms;

class RegistrationSuccessToDistributor
{
	
	private $template = <<<TEMPLATE
Registration Success!
Customer: %Customer%
Product: %Product%
Serial: %Serial%
Key: %Key%
TEMPLATE;
	private $arguments = ['Customer','Product','Serial','Key'];
	
	public $message = "";
	
	public function __construct($Data)
	{

		$this->message = $this->get_message($Data->Customer->name, implode(" ",[$Data->Product->name,$Data->Edition->name,'Edition']), $Data->serialno, $Data->key);
		
	}
	
	private function template(){
		return $this->template;
	}
	
	private function arguments(){
		return $this->arguments;
	}
	
	private function get_filled_arguments($ary){
		return array_combine($this->arguments(),$ary);
	}
	
	private function fill_template($params){
		if(empty($params)) return $this->template();
		$text = $this->template();
		foreach($params as $argument => $value){
			$search = '%'.$argument.'%';
			$replace = $value;
			$text = str_replace($search,$replace,$text);
		}
		return $text;
	}
	
	private function get_message(...$args){
		if(empty($args)) return $this->template();
		return $this->fill_template($this->get_filled_arguments($args));
	}
	
}