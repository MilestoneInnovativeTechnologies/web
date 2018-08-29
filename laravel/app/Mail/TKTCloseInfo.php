<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TKTCloseInfo extends Mailable
{
    use Queueable, SerializesModels;
		public $Ticket;
		private $To, $CC;
	
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
			if(!$Ticket->CreatedBy->Roles->contains('name','supportteam') && !$Ticket->CreatedBy->Roles->contains('name','customer')){
				$CC = [$this->CC, $Ticket->CreatedBy->Logins[0]->email];
				$this->CC = $CC;
			}
			$this->set_view_subject("emails.tkt_closed", "[".$Ticket->code."] Support Ticket Closed!");
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
      return $this->view($this->view)->subject($this->subject);//->to($To,$this->Ticket->Customer->name)->cc($CC)->bcc('thahir@milestoneit.net');
    }
}
