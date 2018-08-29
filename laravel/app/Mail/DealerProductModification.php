<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DealerProductModification extends Mailable
{
    use Queueable, SerializesModels;
		public $Name, $Email, $Products = [], $Parent = [];
		private $Editions = [];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($dealer)
    {
        $Data = $dealer->with("products","editions","logins","parentDetails.details","parentDetails.logins")->whereCode($dealer->code)->first()->toArray();
				$this->Name = $Data["name"];
				$this->Email = $Data["logins"][0]{"email"};
				$this->SetEditionMaster($Data["editions"]);
				$this->SetProducts($Data["products"]);
				$this->SetParent($Data["parent_details"][0]);
				$this->set_view_subject("emails.dealer_product_modification", "Product Authorization Modification");
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
	
	private function SetEditionMaster($EAry){
		foreach($EAry as $EObj)
			if(!array_key_exists($EObj["code"],$this->Editions))
				$this->Editions[$EObj["code"]] = $EObj["name"];
	}

	private function SetProducts($PAry){
		foreach($PAry as $PObj){
			$this->Products[] = [$PObj["name"],$this->Editions[$PObj["pivot"]["edition"]]];
		}
	}

	private function SetParent($Obj){
		$this->Parent["name"] = $Obj["name"];
		$this->Parent["email"] = $Obj["logins"][0]["email"];
		$this->Parent["phone"] = "+".$Obj["details"]["phonecode"]."-".$Obj["details"]["phone"];
	}

}
