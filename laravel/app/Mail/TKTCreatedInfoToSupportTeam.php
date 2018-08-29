<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TKTCreatedInfoToSupportTeam extends Mailable
{
    use Queueable, SerializesModels;
		public $Ticket;
		public $CategoryBreadCrumb, $CAddress;
		private $To, $CC;

		/**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($Ticket)
    {
			$this->Ticket = $Ticket;
			$this->CategoryBreadCrumb = ($Ticket->category) ? $this->GetCategoryBreadCrumb($Ticket->Category) : 'none';
			$this->CAddress = $this->CustomerAddress($Ticket->Customer->Details);
			
			$this->set_view_subject("emails.tkt_new_to_supportteam", "[".$this->Ticket->code."] New Support Ticket");
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

		private function GetCategoryBreadCrumb($Obj){
			$BCAry = [];
			if($Obj) $BCAry[] = $Obj->name;
			if($Obj->parent) array_unshift($BCAry,GetCategoryBreadCrumb($Obj->Parent));
			return implode(" &raquo; ", $BCAry);
		}
	
		private function CustomerAddress($D){
			$AdrParts = [];
			if($D->address1) $AdrParts[] = $D->address1;
			if($D->address2) $AdrParts[] = $D->address2;
			if($D->city) {
				$AdrParts[] = $D->City->name;
				$AdrParts[] = $D->City->State->name;
				$AdrParts[] = $D->City->State->Country->name;
			}
			return implode(', ', $AdrParts);
		}
}
