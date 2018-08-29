<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MCExpireToday extends Mailable
{
    use Queueable, SerializesModels;
		public $Name, $Product, $Code, $StartDate, $EndDate, $ExpireTime;
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
			$this->Code = $Obj->code;
			$this->StartDate = date('d/M/Y',$Obj->start_time);
			$this->EndDate = date('d/M/Y',$Obj->end_time);
			$this->ExpireTime = date('h:i:s A',$Obj->end_time);
			$this->set_view_subject("emails.mc_expire_today", "[Milestone] Maintenance Contract Expiring Today");
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
