<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MCExpired extends Mailable
{
    use Queueable, SerializesModels;
		public $Name, $Product;
		private $To;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($Obj)
    {
			$this->Name = $Obj->Customer->name;
			$this->Product = $this->GetProductName($Obj->Registration->toArray(),$Obj->registration_seq);
			$this->set_view_subject("emails.mc_expired", "[Milestone] Maintenance Contract");
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
		
		private function GetProductName($Ary,$Seq){
			foreach($Ary as $Dets){
				if($Dets['seqno'] != $Seq) continue;
				return implode(" ", [$Dets['product']['name'],$Dets['edition']['name'],'Edition']);
			}
		}
}
