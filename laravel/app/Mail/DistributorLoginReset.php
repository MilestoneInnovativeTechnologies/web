<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DistributorLoginReset extends Mailable
{
    use Queueable, SerializesModels;
		public $Name, $Email, $Company, $CompanyEmail, $CompanyPhone, $Code;

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
				$this->SetCompany();
				$this->set_view_subject("emails.distributor_login_reset", "[Milestone] Login Reset Link");
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
		
		private function SetCompany(){
			$Company = \App\Models\Company::first();
			$this->Company = $Company->name;
			$this->CompanyPhone = $Company->phone;
			$this->CompanyEmail = $Company->email;
		}
}
