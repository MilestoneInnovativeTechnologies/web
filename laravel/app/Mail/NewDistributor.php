<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\City;
use App\Models\Partner;
use App\Models\Product;
use App\Http\Controllers\KeyCodeController;

class NewDistributor extends Mailable
{
    use Queueable, SerializesModels;
		public $Data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($request,$PC,$ID,$Partner)
    {
			$this->SetBasicData($request);
			$this->SetLocationData($request);
			$this->Data["address"] = $this->GetAddress($request);
			$this->Data["phone"] = $this->GetPhone($request);
			$this->SetParentDetails($PC);
			$this->setPrdEdt($request);
			$this->Data['login_key']	=	$this->loginKey($Partner, $request['email'], $ID);
			
			$this->set_view_subject("emails.distributor_new", $this->Data["name"] . ": Welcome to " . $this->Data["parent"]["name"]);
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
	
		private function SetBasicData($request){
			$Basic = ["name","email"];
			foreach($Basic as $F) $this->Data[$F] = $request[$F];
		}
		
		private function SetLocationData($request){
			$Obj = City::with("state.country")->whereId($request["city"])->first()->toArray();
			$this->Data["location"] = implode(", ",[$Obj["name"],$Obj["state"]["name"]]);
			$this->Data["country"] = $Obj["state"]["country"]["name"];
		}
		
		private function GetAddress($request){
			return implode(", ",[$request['address1'],$request['address2']]);
		}
		
		private function GetPhone($request){
			return "+".$request['phonecode']."-".$request['phone'];
		}
	
		private function SetParentDetails($PC){
			$P = \App\Models\Company::first();
			$this->Data["parent"]["name"] = $P->name;
			$this->Data["parent"]["email"] = implode("</u>, <u>",$P->emails);
			$D = $P->Details;
			$this->Data["parent"]["phone"] = $P->phone;
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
		
		private function loginKey($Partner, $Email, $ID){
			$FArray = ['id','partner','email','expiry'];
			$VArray = [$ID, $Partner, $Email, strtotime("+18 Hours")];
			return KeyCodeController::Encode($FArray, $VArray);
		}
}
