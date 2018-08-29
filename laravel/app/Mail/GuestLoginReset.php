<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class GuestLoginReset extends Mailable
{
    use Queueable, SerializesModels;
		public $Email, $Code;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($Logins, $Code)
    {
        $this->Email = $Logins->email;
				$this->Code = $Code;
				
				$this->set_view_subject("emails.guest_login_reset", "[Milestone] Login Reset Link");
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
