<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\KeyCodeController;

use Mail;

class ProductInformationController extends Controller
{
    //
	
	public function index(){
		//return session('_rolename');
		return view('pi.index');
	}
	
	
	public function packages(Request $request){
		if($this->isPrdEdnAuthorized($request->PID, $request->EID)){
			return \App\Models\ProductEditionPackage::where(['product'=>$request->PID,'edition'=>$request->EID])->with(['Packages'	=>	function($Q){
				$Q->select('code','name','type');
			}])->select('package')->get();
		} else {
			return [];
		}
	}
	
	public function download_link(Request $request){
		$PArray = ['product','edition','package','type','packed','expiry','user'];
		$VArray = [$request->PID,$request->EID,$request->PKG,$request->TYP,time(),$this->get_download_expiry($request->VAL),$this->getAuthUser()->partner];
		return Route('software.download',['key'	=>	(new KeyCodeController())->KeyEncode($PArray, $VArray)]);
	}
	
	private function get_download_expiry($D){
		return strtotime("+".$D);
	}
	
	public function my_customers(){
		return $this->get_my_customers()->mapWithKeys(function($item){ return [$item->Logins->first()->email => $item->name]; });
	}
	
	public function get_my_customers(){
		return \App\Models\Customer::all();
		$_Role = session('_rolename'); $Partner = Auth()->guard('api')->user()->partner;
		if($_Role == 'dealer'){
			$Parents = [$Partner];
		} else {
			$Dealers = $this->get_dealers_code($Partner);
			$Parents = array_merge($Dealers,[$Partner]);
		}
		return $this->get_customers_of_parents($Parents);
	}

	private function get_customers_of_parents($Parents){
		return \App\Models\Partner::with([
			'Logins'	=>	function($Q){ $Q->select('partner','email'); },
			'Roles'	=>	function($Q){ $Q->whereName('customer')->select('code','name'); },
			'Parent'	=>	function($Q) use($Parents){ $Q->whereIn('parent',$Parents); }])
			->select('code','name')
			->get()
			->filter(function($A){ return $A->Parent; })
			->filter(function($A){ return $A->Roles->count(); })
			->values()
			//->mapWithKeys(function($item){ return [$item->Logins->first()->email => $item->name]; })
			;
	}
	
	public function my_product_customers(Request $request){
		$Customers = $this->get_my_customers()->pluck('code');
		$ProductUsers = \App\Models\CustomerRegistration::where(['product'=>$request->PID,'edition'=>$request->EID])->whereIn('customer',$Customers)->pluck('customer')->toArray();
		return $this->get_my_customers()->filter(function($Item) use ($ProductUsers){
			return in_array($Item->code,$ProductUsers);
		})->values()->mapWithKeys(function($item){ return [$item->code => [$item->Logins->first()->email,$item->name]]; });
	}
	
	private function get_dealers_code($Partner){
		return \App\Models\PartnerRelation::whereParent($Partner)->with(['childDetails.Roles'])->get()->filter(function($A){ return $A->ChildDetails->Roles->first()->name == 'dealer'; })->pluck('partner')->toArray();
	}
	
	private function getAuthUser(){
		return (Auth()->user())?:Auth()->guard('api')->user();
	}
	
	private function isPrdEdnAuthorized($PID = NULL, $EID = NULL){
		$User = $this->getAuthUser();
		$Where = []; $Where['partner']	=	$User->partner;
		if($PID)  $Where['product']	=	$PID;
		if($EID)  $Where['edition']	=	$EID;
		return (\App\Models\PartnerProduct::where($Where)->count() > 0) ? true : false;
	}
	
	public function sidl(Request $request){

		$PID = $request->PID; $CUS = $request->CUS;
		$EID = $request->EID; $PKG = $request->PKG;
		$ORM = \App\Models\Product::whereCode($PID);
		
		$ORM -> with(['Editions'	=>	function($Q) use($PID, $EID, $PKG) {
			$Q->wherePivot('product', $PID)->oldest('products_editions.level');
			if($EID != "*") { $Q->wherePivot('edition',$EID)->with(['Packages'	=>	function ($Q) use ($PID, $PKG){
				$Q->wherePivot('product',$PID);
				if($PKG != "*") $Q->wherePivot('package',$PKG);
			}]); } else {
				$Q->with(['Packages'	=>	function($Q) use ($PID, $PKG){
					$Q->wherePivot('product',$PID);
					if($PKG != "*") $Q->wherePivot('package',$PKG);
				}]);
			}
		}]);
		
		$Data = $ORM->first();
		Mail::queue(new \App\Mail\ProductDownloadLinks($Data, $CUS));
		
		return $Data;
	}
	
	public function vpd(Request $request){
		$PID = $request->PID; $EID = $request->EID;
		if(!$this->isPrdEdnAuthorized($PID, $EID)) return [];
		return $this->view_product_update($PID, $EID);
	}
	
	private function view_product_update($PID, $EID){
		return \App\Models\PackageVersion::where(['product'	=> $PID, 'edition'	=> $EID, 'status'	=>	'APPROVED'])
			->with(['Package'	=>	function($Q){
				$Q->whereType('Update')->select('code','name');
			}])
			->select('build_date','approved_date','change_log','version_numeric','package','product','edition')
			->latest('version_sequence')
			->get()
			->filter(function($Obj, $key){ return $Obj->Package; })
			->values()
			->first()
			//->get()
			;
	}
	
	public function sputc(Request $request){
		$CUS = $request->CUS; if(empty($CUS)) return 'No customer selected.';
		$Data = \App\Models\CustomerRegistration::where(["product"	=>	$request->PID, "edition"	=>	$request->EID])
			->with("Login.Partner","Product","Edition")
			->whereIn("customer",$CUS)
			->get();
		$Version = $request->VER;
		$Package = $request->PKG;
		return Mail::queue(new \App\Mail\NewProductUpdate($Data,$Version,$Package));
	}
	
}
