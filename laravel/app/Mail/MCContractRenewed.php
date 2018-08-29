<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MCContractRenewed extends Mailable
{
    use Queueable, SerializesModels;
		public $Name, $Product, $Code, $StartDate, $EndDate;
		public $OldCode, $OldExpireDate;

		/**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($Ary)
    {
			$OMC = $Ary[1];
			$NMC = $Ary[0];
			
			$this->Name = $OMC->Customer->name;
			$this->Product = $this->GetProductName($OMC->Registration->toArray(),$OMC->registration_seq);
			
			$this->OldCode = $OMC->code;
			$this->OldExpireDate =  date('d/M/Y',$OMC->end_time);
			
			$this->Code = $NMC->code;
			$this->StartDate = date('d/M/Y',$NMC->start_time);
			$this->EndDate = date('d/M/Y',$NMC->end_time);
			
			$this->set_view_subject("emails.mc_renewed", "[Milestone] Maintenance Contract Renewed.");
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
