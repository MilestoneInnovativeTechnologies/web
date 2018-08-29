<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class GUFDownloadLink extends Mailable
{
    use Queueable, SerializesModels;
		public $gu;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($gu, $email = null)
    {
			$this->gu = $gu;
			$this->set_view_subject("emails.guf_download", "Download Upload file!");
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
