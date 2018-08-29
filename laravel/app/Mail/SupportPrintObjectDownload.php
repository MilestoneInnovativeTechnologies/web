<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SupportPrintObjectDownload extends Mailable
{
    use Queueable, SerializesModels;
		public $Name, $Product, $FName, $FCode, $Time, $User, $Link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($PO, $Key)
    {
			$this->Name = $PO->Customer->name;
			$this->Product = implode(" ",$PO->product) . " Edition";
			$this->FName = $PO->function_name;
			$this->FCode = $PO->function_code;
			$this->Time = date('D d/M/y - h:i A',$PO->time);
			$this->User = $PO->User->name;
			$this->Link = Route('support.printobject.download',$Key);
			$this->set_view_subject("emails.support_print_object_download", "Download Print Object, ".$this->FName);
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
