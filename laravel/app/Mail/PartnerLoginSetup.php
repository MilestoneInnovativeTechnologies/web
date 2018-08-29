<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PartnerLoginSetup extends Mailable
{
    use Queueable, SerializesModels;
		public $Code, $Name, $Email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($Partner,$Code)
    {
        $this->Code = $Code;
        $this->Name = $Partner->name;
        $this->Email = $Partner->Logins[0]->email;

		$this->set_view_subject("emails.partner_login_setup", "[Milestone] Login Setup Link");
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
}
