<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TKTConversationNotify extends Mailable
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
			$this->set_view_subject("emails.tkt_conversation_update", "[".$this->Ticket->code."] Conversation Update");
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
