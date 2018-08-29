<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Partner;
use App\Http\Controllers\KeyCodeController;

class NewCustomer extends Mailable
{
    use Queueable, SerializesModels;
		
		public $Data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($code, $email, $login)
    {
        $this->Data = $this->getPartnerDetails($code);
				$this->Data['login_key']	=	$this->loginKey($code, $email, $login);
			
				$this->set_view_subject("emails.customer_new", $this->Data["name"] . ": Welcome to Milestone Innovative Technologies");
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


		private function getPartnerDetails($code){
			$Partner = Partner::with("logins","parentDetails.details.city.state.country","parentDetails.roles","parentDetails.logins","parentDetails.parentDetails.details.city.state.country","parentDetails.parentDetails.roles","parentDetails.parentDetails.logins","customerProducts.editions")->whereCode($code)->first()->toArray();
			foreach($Partner['customer_products'] as $k => $DA){
				$MED = $DA['pivot']['edition'];
				if(!isset($Products[$DA['code']])) $Products[$DA['code']] = [$DA['name'],[]];
				foreach($DA["editions"] as $l => $EA){
					if($EA['code'] == $MED){
						$Partner["product"] = $DA['name']; $Partner["edition"] = $EA['name'];
					}
				}
			}
			$Partner["parent"] = $this->getParentDetails($Partner['parent_details']);
			$Partner["email"] = $Partner["logins"][0]["email"];
			$Partner["password"] = $Partner["logins"][0]["password"];
			unset($Partner['customer_products'],$Partner['parent_details'],$Partner['logins']);
			return $Partner;
		}

		private function getParentDetails($PAry){
			if($PAry[0]["roles"][0]["name"] != "distributor" && !empty($PAry[0]["parent_details"])) return $this->getParentDetails($PAry[0]["parent_details"]);
			$P = $PAry[0]; $D = $P["details"]; $L = $P['logins'][0]; $R = [];
			$R["name"] = $P['name']; $R["address"] = implode(", ",[$D["address1"],$D["address2"]]); $R['location'] = implode(", ",[$D["city"]["state"]["name"],$D["city"]["name"]]);
			$R["country"] = $D["city"]["state"]["country"]["name"]; $R["email"] = $L["email"]; $R["phone"] = "+".$D["phonecode"]."-".$D["phone"];
			return $R;
		}
		
		private function loginKey($Partner, $Email, $ID){
			$FArray = ['id','partner','email','expiry'];
			$VArray = [$ID, $Partner, $Email, strtotime("+18 Hours")];
			return KeyCodeController::Encode($FArray, $VArray);
		}


}
