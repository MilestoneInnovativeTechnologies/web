<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Feature;
use App\Models\Edition;
use App\Models\ProductEditionFeature;
use App\Models\ProductEditionPackage;
use App\Models\Package;

use Validator;

class ProductController extends Controller
{

		public function create(){
			return view('products.create');
		}

    public function store(Request $request){
			
			$products = new Product();
			//$this->validate($request,$products->MyValidationRules());
			$Rules = $products->MyValidationRules();
			$validator = Validator::make($request->all(),$Rules);
			if($validator->fails()) { return redirect()->back()->withErrors($validator)->withInput(); }
			$Private = ((isset($request->private) && $request->private == "YES") ? "YES" : "NO");
			
			$CreateArray =  array(
				"code"	=>	$products->NextCode(),
				"name"	=>	$request->name,
				"basename"	=>	$request->basename,
				"private"	=>	$Private,
				"description_public"	=>	$request->description,
				"description_internal"	=>	$request->description_internal,	
			);

			$products->create($CreateArray);
			
			$IndexData = array("info"=>true,"type"=>"success","text"=>"The Product: ".$request->name.", Added successfully..","products"=>Product::whereActive("1")->take(10)->get(),"ItemsPerPage"=>10,"PageNumber"=>1);
			return view("products.index")->with(["info"	=> true, "type"	=>	"success","text"	=>	"The Product: ".$request->name.", Added successfully..","products"=>Product::whereActive("1")->take(10)->get(),"ItemsPerPage"=>10,"PageNumber"=>1]);

		}

		public function index(Product $Product, Request $Request){
			$ItemsPerPage = ($Request->items_per_page == null)? 10 : $Request->items_per_page;
			$PageNumber = ($Request->page == null)? 1 : $Request->page;
			$Skip = ($PageNumber-1) * $ItemsPerPage;
			$products = $Product->select("code","name","basename","private","description_internal","description_public")->whereActive("1")->skip($Skip)->take($ItemsPerPage)->get();
			return view("products.index",compact("products","ItemsPerPage","PageNumber"));
		}
		
		public function show(Product $code){
			$code = $code->load("features","editions");
			return view("products.show",compact("code"));
		}
		
		public function edit(Product $code){
			return view("products.edit",compact("code"));
		}
		
		public function update(Product $code){
			$request = Request(); $rules = $code->MyValidationRules(); $fillable = $code->GetMyFillableFields();
			$request->private = ((isset($request->private) && $request->private == "YES") ? "YES" : "NO");
			$myRules = array(); $myFills = array();
			foreach($fillable as $name){
				if($code->$name != $request->$name){
					$myFills[$name] = $request->$name;
					if(array_key_exists($name, $rules)) $myRules[$name] = $rules[$name];
				}
			}

			if(!empty($myRules)){
				$validator = Validator::make($request->all(),$myRules);
				if($validator->fails()) { return redirect()->back()->withErrors($validator)->withInput(); }
			}

			if(!empty($myFills)){
				foreach($myFills as $N => $V) $code->$N = $V;
				$code->save();
			}
			
			return view("products.edit",compact("code"))->with(["info"=>true,"type"=>"success","text"=>"The Product: ".$request->name.", updated successfully.."]);
			
		}
		
		public function destroy(Product $code){
			$name = $code->name; $code->active = "0"; $code->save();
			return redirect()->back()->with(["info"	=> true, "type"	=>	"success","text"	=>	"The Product: " . $name . ", deleted successfully.."]);
		}
		
		public function features(Product $code){
			$PFs = $code->load(array("features"=>function($Q){ $Q->orderBy("order"); }));
			$FeatureObj = new Feature();
			$Features = $FeatureObj->with(array("options"=>function($Qry){ $Qry->orderBy("order"); }))->whereActive("1")->get();
			return view("products.features",compact("PFs","Features"));
		}
		
		public function updatefeatures(Product $code, Request $Request){
			$Features = new Feature(); $Features = $Features->with("options")->get();
			$FeatureOption = $this->GetFeatureOption($Features);
			$UpdateArray = $this->ValidateFeatures($Request->all(),$FeatureOption);
			$code->features()->sync($UpdateArray); $code->load("editions");
			session()->flash("info",true); session()->flash("type","info"); session()->flash("text","Features updated successfully");
			return redirect()->back();//view("products.show",compact("code"));
		}
		
		private function GetFeatureOption($Features){
			$FeatureOptions = array();
			if(!empty($Features) && $Features[0] != ""){
				foreach($Features as $FeatureArray){
					if(in_array($FeatureArray["value_type"],array("OPTION","MULTISELECT"))){
						$FeatureOptions[$FeatureArray['id']] = array();
						foreach($FeatureArray['options'] as $OptArray) array_push($FeatureOptions[$FeatureArray['id']],$OptArray["option"]);
					} else {
						if($FeatureArray["value_type"] == "YES/NO") $FeatureOptions[$FeatureArray['id']] = array("YES","NO");
						else $FeatureOptions[$FeatureArray['id']] = "";
					}
				}
			}
			return $FeatureOptions;
		}
		
		private function ValidateFeatures($Req,$Opt){
			if(!isset($Req["features"]) || !isset($Req["values"]) || empty($Req["features"]) || empty($Req["values"])) return array();
			$Fs = $Req["features"]; $Vs = $Req["values"];
			$RetArray = array();
			if(!empty($Fs)){
				foreach($Fs as $Ord => $FID){
					if(!isset($Vs[$FID]) || is_null($Vs[$FID])) continue;
					if(is_array($Opt[$FID])){
						$Vs1 = $Vs[$FID];
						if(is_array($Vs1)){
							$MVs1 = array();
							foreach($Vs1 as $Vs2){
								if(in_array($Vs2,$Opt[$FID])) $MVs1[] = $Vs2;
							}
							if(!empty($MVs1)) $RetArray[$FID] = array( "order" => $Ord+1, "value" => ("-".implode("-",$MVs1)."-"));
						} else {
							if(in_array($Vs1,$Opt[$FID])) $RetArray[$FID] = array( "order" => $Ord+1, "value" => $Vs1);
						}
					} else {
						if(!is_array($Vs[$FID]) && $Vs[$FID] != "") $RetArray[$FID] = array( "order" => $Ord+1, "value" => $Vs[$FID]);
					}
				}
			}
			return $RetArray;
		}
		
		public function editions(Product $code){
			$Products = $code -> load(array("editions" => function($query){ $query -> orderBy("level"); }));
			$EditionObj = new Edition(); $Editions = $EditionObj -> get();
			return view("products.editions", compact("Products","Editions"));
		}

		public function updateeditions(Product $code, Request $Request){
			$UpdateArray = array();
			if(isset($Request->editions) || !empty($Request->editions)){
				foreach($Request->editions as $EID){
					$level = $Request->level[$EID]; $desc = $Request->description[$EID];
					$UpdateArray[$EID] = array("level" => $level,"description" => $desc);
				}
			}
			$code->editions()->sync($UpdateArray);
			session()->flash("info",true); session()->flash("type","info"); session()->flash("text","Editions updated successfully");
			return redirect()->back();
		}
		
		public function editionfeature($PCode, $ECode){
			$ProductFeatures = Product::where("code",$PCode)->with("features")->get()->first();
			if(!empty($ProductFeatures)) {
				$Product = array($ProductFeatures->code,$ProductFeatures->name,$ProductFeatures->description_public,$ProductFeatures->description_internal);
				$PFs = array();
				if(!empty($ProductFeatures -> features))
					foreach($ProductFeatures -> features as $PFObj){ $PFs[$PFObj['id']] = $PFObj->pivot->value; }
			}
			$EditionFeatures = Edition::where("code",$ECode)->with(array("features" => function($Qry) use ($PCode){ $Qry -> wherePivot("product",$PCode); }))->get()->first();
			if(!empty($EditionFeatures)) {
				$Edition = array($EditionFeatures->code,$EditionFeatures->name,$EditionFeatures->description_public,$EditionFeatures->description_internal);
				$EFs = array();
				if(!empty($EditionFeatures -> features))
					foreach($EditionFeatures -> features as $EFObj){ $EFs[$EFObj['id']] = $EFObj->pivot->value; }
				
			}
			$Features = $this->CleanFeatures(Feature::with("options")->get());
			//return compact("Product","Edition","Features","PFs","EFs");
			return view("products.editionfeatures",compact("Product","Edition","Features","PFs","EFs"));
		}
		
		public function CleanFeatures($FObj){
			if(empty($FObj)) return array();
			$Features = array();
			foreach($FObj as $FA){
				$Features[$FA['id']] = array($FA->name,$FA->description_public,$FA->description_internal,$FA->value_type);
				if(in_array($FA->value_type,array("OPTION","MULTISELECT"))){
					$Features[$FA['id']][4] = array();
					if((is_object($FA->options) || is_array($FA->options)) && !empty($FA->options)){
						foreach($FA->options as $Option){
							$Features[$FA->id][4][$Option['order']] = $Option['option'];
						}
					}
				}
			}
			return $Features;
		}
		
		public function updateeditionfeature(Product $Product, $ECode, Request $Request){
			$PEF = ProductEditionFeature::where(array("product"=>$Product->code,"edition"=>$ECode));
			$RFV = $this->ValidateFeatures($Request->all(),$this->GetFeatureOption(Feature::with("options")->get()));
			$EFS = array();
			if(!empty($RFV))
				foreach($RFV as $F => $OVObj)
					$EFS[] = array("edition" => $ECode,"feature" => $F,"value" => $OVObj["value"],"order" => $OVObj["order"]);
			$PEF->delete();
			$Product->edition_features()->createMany($EFS);
			session()->flash("info",true); session()->flash("type","info"); session()->flash("text","Features updated successfully.");
			return redirect()->back();//view("products.show",compact("code"));
		}
		
		public function packages(Product $Product){
			$Editions = $Product->editions;
			$Packages = Package::all();
			$PC = $Product -> code;
			$EPObj = ProductEditionPackage::where("product",$Product -> code)->get();
			if(!empty($EPObj)){
				$PKG = array();
				foreach($EPObj as $EPO)
					$PKG[$EPO->edition][] = $EPO->package;
			}
			return view("products.packages",compact("Product","Editions","Packages","PKG"));
		}
		
		public function updatepackages(Product $Product, Request $Request){
			$PEP = ProductEditionPackage::where("product",$Product->code);
			$EPS = array();
			if(!empty($Request->packages))
				foreach($Request->packages as $EID => $PIDArray)
					if(is_array($PIDArray) && !empty($PIDArray))
						foreach($PIDArray as $PID)
							$EPS[] = array("edition" => $EID,"package" => $PID);
			$PEP->delete();
			$Product->edition_packages()->createMany($EPS);
			session()->flash("info",true); session()->flash("type","info"); session()->flash("text","Packages updated successfully.");
			return redirect()->back();//view("products.show",compact("code"));
		}

}
