<?php

namespace App\Sms;

class JobApplicationSuccess
{
	
	private $template = <<<TEMPLATE
Thank you, %Name%, for showing interest in Milestone's Job vacancy of, %Title%.
We will keep you updating regarding this application.
TEMPLATE;
	private $arguments = ['Name','Title'];
	
	public $message = "";
	
	public function __construct($Applicant)
	{

	    $this->message = $this->get_message($Applicant->name, $Applicant->Vacancy->title);

		
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