<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Transaction;

class TransactionController extends Controller
{

	public function NewTransaction($Code = "DST",$Date = null,$Distributor,$Description,$Price,$Currency = "INR",$Identifier = NULL,$ExchangeRate = 1,$Type = "-1",$Status	=	"PENDING"){
		Transaction::create(["code"	=>	$Code,
												 "date"	=>	($Date)?:date('Y-m-d'),
												 "distributor"	=>	$Distributor,
												 "description"	=>	$Description,
												 "price"	=>	$Price,
												 "currency"	=>	$Currency,
												 "exchange_rate"	=>	$ExchangeRate,
												 "type"	=>	$Type,
												 "status"	=>	$Status,
												 "user"	=>	Request()->user()->partner,
												 "identifier"	=>	$Identifier]);
	}
	
	public function TransactionConfirmed($Code){
		$this->alterStatus($Code);
	}
	
	public function TransactionConfirmedIdentifier($Identifier){
		Transaction::whereIdentifier($Identifier)->update(["status"	=>	"ACTIVE"]);
	}
	
	public function CustomerRegistration($Customer,$Product,$Edition,$Identifier){
		$Distributor = $this->GetDistributor($Customer);
		$Description = $this->GetDescription($Customer,$Product,$Edition);
		$PriceDetails = $this->GetPriceDetails($Distributor,$Product->code,$Edition->code);
		$this->NewTransaction("DST",date('Y-m-d'),$Distributor->code,$Description,$PriceDetails->price,$PriceDetails->currency,$Identifier);
	}
	
	private function GetDistributor($Partner){
		if($Partner->ParentDetails[0]->Roles->contains('name','company')) return $Partner;
		return $this->GetDistributor($Partner->ParentDetails[0]);
	}
	
	private function GetDescription($Customer,$Product,$Edition){
		return "Product Registration of ".$Product->name." ".$Edition->name." Edition for client, ".$Customer->name;
	}
	
	private function GetPriceDetails($Partner,$Product,$Edition){
		$PL = $Partner->with(["Pricelist.Details"	=>	function($Q) use($Product,$Edition){
			$Q->whereProduct($Product)->whereEdition($Edition)->select("pricelist","price","mrp","currency");
		}])->whereCode($Partner->code)->first()->Pricelist->first();
		return ($PL->Details->count()) ? $PL->Details[0] : collect([(object)["pricelist"=>$PL->code,"product"=>$Product,"edition"=>$Edition,"currency"=>"INR","price"=>"0.00"]])->first();
	}
	
	public function CustomerRegistrationInitialized($Data){
		$this->CustomerRegistration($Data->Customer,$Data->Product,$Data->Edition,$Data->requisition);
	}
	
	public function alterStatus($Code){
		$CurrentToNew = ["ACTIVE"=>"INACTIVE","INACTIVE"=>"PENDING","PENDING"=>"ACTIVE"];
		$TXN = Transaction::find($Code);
		$TXN->update(["status"	=>	$CurrentToNew[$TXN->status]]);
		return $TXN;
	}
	
	public function apiUpdatePrice($Code,$Param){
		$TXN = Transaction::find($Code);
		$PA = explode("|",$Param);
		$TXN->update(["price"	=>	$PA[0],"exchange_rate"	=>	$PA[1],"amount"	=>	$PA[2]]);
		return $TXN;
	}
	
	public function CompanyNewTransaction($Distributor, Request $Request){
		if(empty($Request->identifier)) return redirect()->back()->with(["info"=>true,"type"=>"info","text"=>"No transactions to create."]);
		foreach($Request->identifier as $Identifier){
			$r = $Request->$Identifier;
			$this->NewTransaction("CMP",$r["date"],$Distributor,$r["description"],$r["price"],$r["currency"],$Identifier,$r["exchange_rate"],$r["type"]."1",$r["status"]);
		}
		return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Transactions created successfully."]);
	}

}
