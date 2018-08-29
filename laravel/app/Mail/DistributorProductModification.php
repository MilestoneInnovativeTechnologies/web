<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DistributorProductModification extends Mailable
{
    use Queueable, SerializesModels;
		public $Name, $Email, $Products = [], $Company = [], $Currency;
		private $Editions = [], $PLMaster = [];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($distributor)
    {
        $Data = $distributor->with("products","editions","pricelist.details","logins","parentDetails.details","parentDetails.logins")->whereCode($distributor->code)->first()->toArray();
				$this->Name = $Data["name"];
				$this->Email = $Data["logins"][0]{"email"};
				$this->SetEditionMaster($Data["editions"]);
				$this->SetPLMaster($Data["pricelist"][0]["details"]);
				$this->SetProducts($Data["products"]);
				$this->SetCompany();
			
				$this->set_view_subject("emails.distributor_product_modification", "[Milestone] Product Authorization Modification");
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
	
	private function SetPLMaster($PLAry){
		foreach($PLAry as $PLObj){
			$Key = $this->GetPLKey($PLObj["product"],$PLObj["edition"]);
			if(!array_key_exists($Key,$this->PLMaster))
				$this->PLMaster[$Key] = array_map("round",[$PLObj["mop"],$PLObj["price"],$PLObj["mrp"]]);
		}
		$this->Currency = $PLAry[0]["currency"];
	}
	
	private function SetProducts($PAry){
		foreach($PAry as $PObj){
			$PLKey = $this->GetPLKey($PObj["code"],$PObj["pivot"]["edition"]);
			if(!array_key_exists($PLKey,$this->PLMaster)) $this->PLMaster[$PLKey] = ["-","-","-","-"];
			$this->Products[] = array_merge([$PObj["name"],$this->Editions[$PObj["pivot"]["edition"]]],$this->PLMaster[$PLKey]);
		}
	}
	
	private function GetPLKey($P,$E){
		return implode(":",[$P,$E]);
	}
	
	private function SetCompany(){
		$Company = \App\Models\Company::first();
		$this->Company["name"] = $Company->name;
		$this->Company["email"] = $Company->email;
		$this->Company["phone"] = $Company->phone;
	}

}
