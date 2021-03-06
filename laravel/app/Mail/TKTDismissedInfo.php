<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TKTDismissedInfo extends Mailable
{
    use Queueable, SerializesModels;
		public $Ticket;
	
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($Ticket)
    {
			$this->Ticket = $Ticket;
			$this->To = $Ticket->Customer->Logins[0]->email;
			$this->CC = $Ticket->Team->Team->Logins[0]->email;
			
			$this->set_view_subject("emails.tkt_dismissed", "[".$this->Ticket->code."] Ticket Dismissed!!");
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
