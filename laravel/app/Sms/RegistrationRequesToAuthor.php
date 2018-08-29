<?php

namespace App\Sms;

class RegistrationRequesToAuthor
{
	
	private $template = <<<TEMPLATE
New Registration Request
Customer: %Customer%
Distributor: %Distributor%
Time: %Time%
TEMPLATE;
	private $arguments = ['Customer','Distributor','Time'];
	
	public $message = "";
	
	public function __construct($Customer)
	{

		$this->message = $this->get_message($Customer->name, $Customer->get_distributor()->name, date('D d/M/y h:i a'));
		
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