<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomerLoginReset extends Mailable
{
    use Queueable, SerializesModels;
		public $Name, $Email, $Distributor, $DistributorEmail, $DistributorPhone, $Code;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($Partner, $Code)
    {
        $this->Code = $Code;
        $this->Name = $Partner->name;
        $this->Email = $Partner->Logins[0]->email;
				$this->SetDistributor($Partner);
			
				$this->set_view_subject("emails.customer_login_reset", "[Milestone] Login Reset Link");
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
	
		
		private function SetDistributor($Partner){
			$Distributor = $this->GetDistributor($Partner);
			$this->Distributor = $Distributor->name;
			$Details = $Distributor->Details;
			$this->DistributorPhone = '+'.$Details->phonecode.'-'.$Details->phone;
			$this->DistributorEmail = $Distributor->Logins[0]->email;
		}
	
		private function GetDistributor($Partner){
			if($Partner->Parent->ParentDetails->Roles[0]->name == 'distributor') return $Partner->Parent->ParentDetails;
			else return $Partner->Parent->ParentDetails->Parent->ParentDetails;
		}
}
