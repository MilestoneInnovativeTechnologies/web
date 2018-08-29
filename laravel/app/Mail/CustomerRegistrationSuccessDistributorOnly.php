<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomerRegistrationSuccessDistributorOnly extends Mailable
{
    use Queueable, SerializesModels;
		
		public $Serial, $Key;
		public $Name, $Email;
		public $Product, $Edition, $Software;
		public $Distributor = [], $Company = [];
	
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($CS)
    {
			$this->SetSerial($CS);
			$this->SetBasic($CS);
			$this->SetProduct($CS);
			$this->SetParent($CS);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
				$View = 'emails.customer_registration_success_distributor_only';
				//$To	=	[(object)["name"	=>	$this->Name, "email"	=>	$this->Email]];
				$To = [(object)["name"	=>	$this->Distributor['name'], "email"	=>	$this->Distributor['email']]];
				$Subject	=	"[Milestone] Registration Approved for " . $this->Name;
				event(new \App\Events\LogSentMail($To,$Subject,$View));
				return $this->view($View)->to($To)->subject($Subject)->bcc('thahir@milestoneit.net');
    }
	
	private function SetSerial($CS){
		$this->Serial = $CS->serialno;
		$this->Key	=	$CS->key;
	}
	
	private function SetBasic($CS){
		$this->Name = $CS->Customer->name;
		$this->Email	=	$CS->Customer->Logins[0]->email;
	}
	
	private function SetProduct($CS){
		$this->Product = $CS->Product->name;
		$this->Edition = $CS->Edition->name;
		$this->Software	=	$CS->Product->name . " " . $CS->Edition->name . " Edition";
	}
	
	private function SetParent($CS){
		$P = $CS->Customer->ParentDetails[0];
		if($P->ParentDetails[0]->Roles->contains('name','company')) {
			$this->SetDistributor($P);
			$this->SetCompany($P->ParentDetails[0]);
		}	else {
			$this->SetDistributor($P->ParentDetails[0]);
			$this->SetCompany($P->ParentDetails[0]->ParentDetails[0]);
		}
	}
	
	private function SetDistributor($C){
		$this->Distributor["name"] = $C->name;
		$this->Distributor["email"] = $C->Logins[0]->email;
		$D = $C->Details;
		$this->Distributor["phone"]	=	"+" . $D->phonecode . "-" . $D->phone;
		$this->Distributor["address"]	=	$D->address1 . ", " . $D->address2;
		$this->Distributor["location"]	=	($D->city) ? ($D->City->name . ", " . $D->City->State->name) : '';
		$this->Distributor["country"]	=	($D->city) ? ($D->City->State->Country->name) : '';
	}
	
	private function SetCompany($C){
		$C = \App\Models\Company::first();
		$this->Company["name"] = $C->name;
		$this->Company["email"] = $C->email;
		$this->Company["phone"]	=	$C->phone;
		$this->Company["address"]	=	$C->address[0] . ", " . $C->address[1];
		$this->Company["location"]	=	$C->address[2] . ", " . $C->address[3];
		$this->Company["country"]	=	$C->address[4];
	}
	
}
