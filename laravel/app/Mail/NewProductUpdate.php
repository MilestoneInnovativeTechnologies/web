<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewProductUpdate extends Mailable
{
    use Queueable, SerializesModels;
		
		public $Version, $Product, $Edition, $Package, $MailDownloadKey;
		protected $Emails = [], $Data, $Key;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($Data, $Version, $Package)
    {
      $this->Package = $Package;
      $this->Version = $Version;
			$this->Data = $Data;
			$First = $Data->first();
			$this->Product = $First->Product->name;
			$this->Edition = $First->Edition->name;
			$this->fetchEmails($Data);
			$this->Key = $this->MailKey();
			$this->MailDownloadKey = (new \App\Http\Controllers\KeyCodeController())->KeyEncode(["product","edition","package","key"],[$First->Product->code,$First->Edition->code,$Package,$this->Key]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
			$View = 'emails.new_software_update';
			$To = 'new_software_updates.ml@milestoneit.net';
			$Subject = "[Milestone] New Software Update";
			$Bcc = $this->Emails;
			event(new \App\Events\LogSentMail($To,$Subject,$View,["BCC"=>$Bcc->toArray(),"Key"=>$this->Key]));
      return $this->view($View)->subject($Subject)->to($To,'Mailing List - New Software Updates')->bcc($Bcc)->bcc('thahir@milestoneit.net');
    }
	
		private function fetchEmails($Data){
			$this->Emails = $Data->map(function($item, $key){
				return (object) ["email"	=>	$item->Login->email, "name"	=>	$item->Login->Partner->name];
			});
		}
	
		private function MailKey(){
			return md5(date("YmdHis"));
		}
}
