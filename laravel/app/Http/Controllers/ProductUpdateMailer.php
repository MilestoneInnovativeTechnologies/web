<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Mail;

class ProductUpdateMailer extends Controller
{
  
	public function index(){
		return view("product_update_mailer.index");
	}
	
	public function geteditions(\App\Models\Product $product){
		return $product->Editions->pluck("name","code");
	}
	
	public function getpackages($product, $edition){
		return \App\Models\ProductEditionPackage::where(["product"	=> $product, "edition"	=>	$edition])->with(["Package"	=> function($q){
			$q->select("code","name")->whereType("Update");
		}])->get()->pluck("Package")->filter()->pluck("name","code");
	}
	
	public function getdistributors($PID, $EID){
		return \App\Models\PartnerProduct::where(['product'=>$PID,'edition'=>$EID])
			->select('product','edition','partner')
			->with(['Partner'	=>	function($Q){
				$Q->select('code','name')
					->with(['Logins'	=>	function($Ql){
						$Ql->whereStatus('ACTIVE')->select('id','partner','email')
							->with(['Roles'	=>	function($Qr){
								$Qr->select('login','rolename');
							}])->whereHas('Roles',function($Qr1){ $Qr1->whereRolename('distributor'); });
					}]);
			}])
			->get()
			->filter(function($item){ return $item->Partner->Logins->isNotEmpty(); })
			->values()
			->mapWithKeys(function($item){
				return [$item->Partner->code => [$item->Partner->Logins[0]->email, $item->Partner->name]];
			})
			;
	}
	
	public function pvd($product, $edition, $package){
		return \App\Models\PackageVersion::where(["product"	=> $product, "edition"	=>	$edition, "package"	=>	$package, "status"	=>	"APPROVED"])->latest('version_sequence')->with(["product"	=>	function($q){
			$q->select("code","name");
		},"edition"	=>	function($q){
			$q->select("code","name");
		},"package"	=>	function($q){
			$q->select("code","name");
		}])->first()?:[];//get()->groupBy("version_sequence")->pop()?:[];
	}
	
	public function search(Request $request){
		$ORM = \App\Models\CustomerRegistration::select("customer","presale_enddate AS ped","presale_extended_to AS pex")
			->where(["product"	=>	$request->product, "edition"	=>	$request->edition])
			->with(["Customer"	=>	function($q) use($request){
				$q->select("code","name");
				if($request->name != "") $q->where("name","like","%".$request->name."%");
			},"Customer.Logins"	=>	function($q) use($request){
				$q->select("partner","email");
				if($request->email != "") $q->where("email","like","%".$request->email."%");
			}]);
		if($request->presale == "true") $ORM->where(function($q){
				$q->where("presale_enddate",">=",date("Y-m-d"))->orWhere("presale_extended_to",">=",date("Y-m-d"));
			});
		return $ORM->get()->filter(function($item, $key){
			return !( $item->Customer == NULL || count($item->Customer->Logins) == 0 );
		})->values();
	}
	
	public function sum(Request $request){
		$Data = \App\Models\CustomerRegistration::where(["product"	=>	$request->product, "edition"	=>	$request->edition])
			->with("Login.Partner","Product","Edition")
			->whereIn("customer",$request->customers)
			->get();
		$Version = $request->version;
		$Package = $request->package;
		if($Data->isNotEmpty()) Mail::queue(new \App\Mail\NewProductUpdate($Data,$Version,$Package));
		if($request->distributors > 0){
			$Data = \App\Models\PartnerProduct::where(["product"	=>	$request->product, "edition"	=>	$request->edition])
				->with("Login.Partner","Product","Edition")
				->whereIn("partner",$request->customers)
				->get();
			if($Data->isNotEmpty()) Mail::queue(new \App\Mail\NewProductUpdate($Data,$Version,$Package));
		}
	}

}
