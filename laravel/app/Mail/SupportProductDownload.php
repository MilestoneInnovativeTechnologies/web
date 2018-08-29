<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SupportProductDownload extends Mailable
{
    use Queueable, SerializesModels;
	
		public $Ticket, $Name, $Team, $Link, $Product, $ProductDetails, $Edition, $EditionDetails, $Package, $FullProduct, $Version;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($Package, $Ticket, $Link, $Team, $User)
    {
			$this->Product = $Package->Product->name;
			$this->Edition = $Package->Edition->name;
			$this->Package = $Package->Package->name;
			$this->FullProduct = $this->Product . " " . $this->Edition . " edition";
			$this->ProductDetails = $Package->Product->description_public;
			$this->EditionDetails = $Package->Product->Editions()->whereCode($Package->edition)->first()->pivot->description;
			$this->Version = $Package->version_numeric;

			$this->Ticket = $Ticket->code;
			$this->Name = $Ticket->Customer->name;
			
			$this->Link = $Link;
			$this->Team = $Team;

			$this->set_view_subject("emails.package_download_support", "[".$this->Team."] Product Download Links and Details!");
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
