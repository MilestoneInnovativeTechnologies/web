<?php

namespace App\Sms;

class TKTNewInfoToDistributor
{
	
	private $template = <<<TEMPLATE
Ticket
No: %Code%
Title: %Title%
has successfully created for
%Customer%
Log to your panel for more info!
TEMPLATE;
	private $arguments = ['Code','Title','Customer'];
	
	public $message = "";
	
	public function __construct($Ticket)
	{

		$this->message = $this->get_message($Ticket->code, $Ticket->title, $Ticket->Customer->name);
		
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