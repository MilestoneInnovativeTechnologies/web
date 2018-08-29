<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PODownload extends Mailable
{
    use Queueable, SerializesModels;
		public $CPO, $Key;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($CPO)
    {
			$PAry = ['customer','product','function_name','po_code','file'];
			$VAry = [$CPO->Customer->name, implode(" ",$CPO->Product).' Edition', $CPO->function_name, $CPO->code, $CPO->file];
			$this->Key = \App\Http\Controllers\KeyCodeController::Encode($PAry, $VAry);
			$this->CPO = $CPO;
			
			$this->set_view_subject("emails.po_download", "Download Print Object!");
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
