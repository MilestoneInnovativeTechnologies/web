<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewProductUpdate2 extends Mailable
{
    use Queueable, SerializesModels;
		
		public $Version, $Product, $Edition, $Package, $MailDownloadKey;
		protected $To, $Data, $Key;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($Data, $Version, $Package, $To)
    {
      $this->Package = $Package;
      $this->Version = $Version;
			$this->To = $To;
			$First = $Data->first();
			$this->Product = $First->Product->name;
			$this->Edition = $First->Edition->name;
			$this->Key = $this->MailKey();
			$this->MailDownloadKey = (new \App\Http\Controllers\KeyCodeController())->KeyEncode(["product","edition","package","key"],[$First->Product->code,$First->Edition->code,$Package,$this->Key]);
			$this->set_view_subject("emails.new_software_update", "[Milestone] New Software Update!");
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

		private function MailKey(){
			return md5(date("YmdHis"));
		}
}
