<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SREQCreated extends Mailable
{
    use Queueable, SerializesModels;
		public $SR;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($SR)
    {
			$this->SR = $SR->load(['User.Roles','Supportteam.Logins']);
			$this->set_view_subject("emails.sreq_created", "New Service Request Raised!");
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
