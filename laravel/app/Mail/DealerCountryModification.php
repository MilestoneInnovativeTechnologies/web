<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DealerCountryModification extends Mailable
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
			$this->SetParent($Data["parent_details"][0]);
			$this->SetCountries($Data["countries"]);
			$this->set_view_subject("emails.dealer_country_modification", "Modifications in Country Lists");
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

	private function SetParent($Obj){
		$this->Parent["name"] = $Obj["name"];
		$this->Parent["email"] = $Obj["logins"][0]["email"];
		$this->Parent["phone"] = "+".$Obj["details"]["phonecode"]."-".$Obj["details"]["phone"];
	}
	
	private function SetCountries($CAry){
		foreach($CAry as $CObj)
			$this->Countries[] = $CObj["name"];
	}

}
