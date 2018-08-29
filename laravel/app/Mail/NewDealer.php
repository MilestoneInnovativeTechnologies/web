<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\City;
use App\Models\Product;
use App\Models\Partner;
use App\Http\Controllers\KeyCodeController;

class NewDealer extends Mailable
{
    use Queueable, SerializesModels;
		public $Data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($reqs,$pc,$id,$partner)
    {
				$this->Data["email"]	=	$reqs["email"];
				$this->Data["name"]	=	$reqs["name"];
				$this->Data["address"]	=	$this->getAddress($reqs);
				$this->setLocations($reqs);
				$this->Data["phone"] = "+" . $reqs["phonecode"] . "-" . $reqs["phone"];
				$this->setPrdEdt($reqs);
				$this->setParent($pc);
				$this->Data['login_key']	=	$this->loginKey($partner, $reqs["email"], $id);
				$this->set_view_subject("emails.dealer_new", "Congratulations " . $this->Data["name"]);
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
	
		private function getAddress($req){
			return implode(", ",[$req["address1"],$req["address2"]]);
		}
	
		private function setLocations($req){
			$Obj = City::with("state.country")->whereId($req["city"])->first()->toArray();
			$this->Data["location"] = implode(", ",[$Obj["name"],$Obj["state"]["name"]]);
			$this->Data["country"] = $Obj["state"]["country"]["name"];
		}
		
		private function setPrdEdt($req){
			$ReqProducts = array_values($req["product"]); $ReqEditions = array_values($req["edition"]);
			$Products = []; $Editions = [];
			Product::with("editions")->whereIn("code",$ReqProducts)->get()->map(function($item,$key) use(&$Products,&$Editions) {
				if(!array_key_exists($item->code,$Products)) $Products[$item->code] = $item->name;
				$PREditions = $item->editions->toArray(); foreach($PREditions as $EObj){
					if(!array_key_exists($EObj["code"],$Editions)) $Editions[$EObj["code"]] = $EObj["name"];
				}
			});
			$this->Data["products"] = [];
			foreach($ReqProducts as $k => $PC){
				$this->Data["products"][] = [$Products[$PC],$Editions[$ReqEditions[$k]]];
			}
		}
	
		private function setParent($code){
			$PObj = Partner::with("details.city.state.country","logins")->whereCode($code)->first();
			$this->Data["parent"]["name"] = $PObj->name;
			$this->Data["parent"]["address"] = $this->getAddress($PObj->details);
			$DAry = $PObj->details->toArray();
			$this->Data["parent"]["location"] = implode(", ",[$DAry["city"]["name"],$DAry["city"]["state"]["name"]]);
			$this->Data["parent"]["country"] = $DAry["city"]["state"]["country"]["name"];
			$this->Data["parent"]["email"] = $PObj->logins[0]->email;
			$this->Data["parent"]["phone"] = "+" . $DAry["phonecode"] . "-" . $DAry["phone"];
		}
		
		private function loginKey($Partner, $Email, $ID){
			$FArray = ['id','partner','email','expiry'];
			$VArray = [$ID, $Partner, $Email, strtotime("+18 Hours")];
			return KeyCodeController::Encode($FArray, $VArray);
		}
		
}
