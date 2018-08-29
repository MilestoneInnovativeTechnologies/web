<?php

namespace App\Sms;

class TKTClosedToDistributor
{
	
	private $template = <<<TEMPLATE
A Support ticket %Code%, created by you on %Date% has been closed.
Please login to your panel for further actions.
TEMPLATE;
	private $arguments = ['Code','Date'];
	
	public $message = "";
	
	public function __construct($Ticket)
	{

		$this->message = $this->get_message($Ticket->code, date('d/m',strtotime($Ticket->created_at)));
		
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