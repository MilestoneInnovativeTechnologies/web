<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TKTRequestForComplete extends Mailable
{
    use Queueable, SerializesModels;
		public $Ticket, $Ago, $Name;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($Ticket)
    {
      $this->Ticket = $Ticket;
			$this->Ago = $Ago = floor((time()-$Ticket->Cstatus->start_time)/(24*60*60)) . ' Days ago';;
			$this->Name = $Ticket->Customer->name;
			
			$this->set_view_subject("emails.tkt_req_complete", "[Milestone] Ticket Status Remainder");
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
