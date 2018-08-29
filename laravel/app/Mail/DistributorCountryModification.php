<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DistributorCountryModification extends Mailable
{
    use Queueable, SerializesModels;
		public $Name, $Email, $Parent=[], $Countries;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($Partner)
    {
			$Data = $Partner->with("details","logins","countries","parentDetails.details","parentDetails.logins")->whereCode($Partner->code)->first()->toArray();
			$this->Name = $Data["name"];
			$this->Email = $Data["logins"][0]["email"];
			$this->SetParent();
			$this->SetCountries($Data["countries"]);
			$this->set_view_subject("emails.distributor_country_modification", "[Milestone] Modifications in Country Lists");
    }
	
		private function set_view_subject($view, $subject){
			
			$this->view = $view;
			$this->subject = $subject;
			
		}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view($this->view)->subject($this->subject);
    }
	
	private function SetParent(){
		$Company = \App\Models\Company::first();
		$this->Parent["name"] = $Company->name;
		$this->Parent["email"] = $Company->email;
		$this->Parent["phone"] = $Company->phone;
	}
	
	private function SetCountries($CAry){
		foreach($CAry as $CObj)
			$this->Countries[] = $CObj["name"];
	}

}
