<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ThirdPartyAppDownload extends Mailable
{
    use Queueable, SerializesModels;
		public $Model, $Url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($tpa, $url)
    {
			$this->Model = $tpa;
			$this->Url = $url;
			$this->set_view_subject("emails.thirdparty_app_download", "Software Download Link!");
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
