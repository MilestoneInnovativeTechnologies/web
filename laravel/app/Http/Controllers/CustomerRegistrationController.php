<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerRegistration as CR;

class CustomerRegistrationController extends Controller
{
	
	private $CustomizedORMFields = ['product' => 'ofField','edition' => 'ofField','customer' => 'ofCustomer'/*,'type' => 'ofType','period' => 'ofPeriod','till' => 'ofTill'*/];
	private $CustomizedORMFieldsForCustomers = ['dealer' => 'DealerCustomers','distributor' => 'DistributorCustomers'];
	
	public function index(){
		//return $this->GetCustomizedOrm(['distributor'=>'DIST000020','type'=>'reg'])->get();
	}
	
	public function GetCustomizedOrm($Ary = []){
		$CR = new CR; if(empty($Ary)) return $CR;
		foreach($this->CustomizedORMFields as $Fld => $Scope) if(array_key_exists($Fld,$Ary) && $Ary[$Fld]) $CR = $CR->$Scope($Fld,$Ary[$Fld]);
		foreach($this->CustomizedORMFieldsForCustomers as $Fld => $Method) if(array_key_exists($Fld,$Ary) && $Ary[$Fld]) $CR = $CR->ofCustomer($this->$Method($Ary[$Fld]));
		return $CR;
	}

	private function DistributorCustomers($Distributor){
		$Distributor = \App\Models\Distributor::find($Distributor);
		$Customers = $this->DistributorNativeCustomers($Distributor);
		$Dealers = $this->DistributorDealers($Distributor);
		$Customers = array_merge($Customers,$this->DealerCustomers($Dealers));
		return $Customers;
	}
	
	private function DealerCustomers($Dealer){
		if(empty($Dealer)) return [];
		$PR = new \App\Models\PartnerRelation;
		$ORM = (is_array($Dealer)) ? $PR->whereIn('parent',$Dealer) : $PR->where('parent',$Dealer);
		return $ORM->pluck('partner')->toArray();
	}
	
	private function DistributorDealers($Distributor){
		if(gettype($Distributor) != "object") $Distributor = \App\Models\Distributor::find($Distributor);
		return $Distributor->Dealers->pluck('partner')->toArray();
	}
	
	private function DistributorNativeCustomers($Distributor){
		if(gettype($Distributor) != "object") $Distributor = \App\Models\Distributor::find($Distributor);
		return $Distributor->Customers->pluck('partner')->toArray();
	}
	
//	public function regdetails(Request $request){
//		$ORM = $this->GetCustomizedOrm($request->all())->own()->with(['Product','Edition','Customer' => function($Q){ $Q->with('ParentDetails.ParentDetails'); }]);
//		if($request->period && $period = $request->period) $ORM = $ORM->where('created_at','>=',date('Y-m-d 00:00:00',( (is_numeric($period)) ? $period : strtotime('-'.$period,strtotime(date('Y-m-d 00:00:00')) ))));
//		if($request->till && $till = $request->till) $ORM = $ORM->where('created_at','<',date('Y-m-d 00:00:00',( (is_numeric($till)) ? $till : strtotime('-'.$till,strtotime(date('Y-m-d 00:00:00')) ))));
//		$Registrations = $ORM->get();
//		return view('crd.index',compact('Registrations'));
//	}
	
	public function regdetails(Request $request){
		$ORM = $this->GetCustomizedOrm($request->all())->own()->with(['Product','Edition','Customer' => function($Q){ $Q->with('ParentDetails.ParentDetails'); }]);
		//$ORM = CR::own()->with(['Customer' => function($Q){ $Q->with('ParentDetails.ParentDetails'); },'Product' => function($Q){ $Q->select('code','name'); },'Edition' => function($Q){ $Q->select('code','name'); }]);
		$ORM = ($request->type) ? (($request->type == 'reg') ? ($ORM->whereNotNull('registered_on')->latest('registered_on')) : ($ORM->whereNull('registered_on')->latest('updated_at'))) : $ORM;
		if($request->period && $period = $request->period) $ORM = $ORM->whereRaw('IFNULL(`registered_on`,DATE(`created_at`)) >= "' . date('Y-m-d',( (is_numeric($period)) ? $period : strtotime('-'.$period,strtotime(date('Y-m-d 00:00:00')) ))) . '"');
		if($request->till && $till = $request->till) $ORM = $ORM->whereRaw('IFNULL(`registered_on`,DATE(`created_at`)) < "' . date('Y-m-d',( (is_numeric($till)) ? $till : strtotime('-'.$till,strtotime(date('Y-m-d 00:00:00')) ))) . '"');
		//return $ORM->toSql();
		$Registrations = $ORM->get();
		return view('crd.index',compact('Registrations'));
	}
	
}
