<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;


class NewRegRequest extends Mailable
{
    use Queueable, SerializesModels;
	
		public $ProductName, $CustomerName, $CustomerAddress, $CustomerEmail, $CustomerPhone, $CustomerIndustry, $CompanyName, $CompanyEmail, $DistributorName, $DistributorEmail, $LicFile, $Requisition;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($CR, $Data, $User, $Company)
    {
			$this->SetCompany($Company);
			$this->ProductName = $Data['SoftwareName'];
			$this->LicFile = $Data['lic_file']; $this->Requisition = $Data['requisition'];
			$this->SetCustomer($CR);
			$this->SetDistributor($User, $CR);
			
			$this->set_view_subject("emails.new_reg_req", "[Registration Request] " . $this->CustomerName);
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
        return $this->view($this->view)->subject($this->subject)->replyTo($this->CustomerEmail, $this->CustomerName)->attach(storage_path("app/".$this->LicFile),["as"	=>	(str_replace(" ",".",$this->CustomerName).".lic")]);
    }
	
	private function SetCompany($Company){
		$C = \App\Models\Company::first();
		$this->CompanyName = $C->name;
		$this->CompanyEmail = $C->email;
	}
	
	private function SetCustomer($CR){
		$Data = $CR->with("customer.details.city.state.country","customer.logins","customer.details.industry")->first()->toArray();
		$this->CustomerName = $Data['customer']['name']; $this->CustomerEmail = $Data['customer']['logins'][0]['email'];
		$Details = $Data['customer']['details'];
		$this->CustomerIndustry = $Details['industry']['name']; $this->CustomerPhone = "+".$Details['phonecode']."-".$Details['phone'];
		$this->CustomerAddress = implode("<br/>",[implode(", ",[$Details['address1'],$Details['address2']]),implode(", ",[$Details['city']['name'],$Details['city']['state']['name']]),$Details['city']['state']['country']['name']]);
	}
	
	private function SetDistributor($User, $CR){
		$Reg = $CR->first();
		$D = $Reg->Customer->get_distributor();
		//$D = $this->GetDistributor($User);
		$this->DistributorName = $D->name;
		$this->DistributorEmail = $D->Logins()->first()->email;
	}
	
	private function GetDistributor($User){
		$roles = $User->partner()->first()->roles->toArray();
		if(in_array("distributor",array_column($roles,"name"))) return $User->partner()->first();
		return $User->partner()->first()->parentDetails()->first();
	}
	
}
