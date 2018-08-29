<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;
use Validator;

use App\Models\Product;
use App\Models\Edition;
use App\Models\ProductEditionPackage;
use App\Models\PackageVersion;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Package $Package, Request $Request)
    {
			$ItemsPerPage = ($Request->items_per_page == null)? 10 : $Request->items_per_page;
			$PageNumber = ($Request->page == null)? 1 : $Request->page;
			$Skip = ($PageNumber-1) * $ItemsPerPage;
			$packages = $Package->select("code","name","base_name","type","description_internal","description_public")->whereActive("1")->skip($Skip)->take($ItemsPerPage)->get();
			return view("packages.index",compact("packages","ItemsPerPage","PageNumber"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
			return view('packages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
			$packages = new Package();
			//$this->validate($request,$products->MyValidationRules());
			$Rules = $packages->MyValidationRules();
			$validator = Validator::make($request->all(),$Rules);
			if($validator->fails()) { return redirect()->back()->withErrors($validator)->withInput(); }
			

			$packages->create(array(
				"code"	=>	$packages->NextCode(),
				"name"	=>	$request->name,
				"base_name"	=>	$request->base_name,
				"type"	=>	$request->type,
				"description_public"	=>	$request->description,
				"description_internal"	=>	$request->description_internal,	
			));
			
			$IndexData = array("info"=>true,"type"=>"success","text"=>"The new Package: ".$request->name.", Added successfully..","packages"=>$packages->take(10)->get(),"ItemsPerPage"=>10,"PageNumber"=>1);
			return view("packages.index",$IndexData);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function show(Package $package)
    {
			$code = $package;
			return view("packages.show",compact("code"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function edit(Package $package)
    {
			$code = $package;
			return view("packages.edit",compact("code"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Package $package)
    {
			$rules = $package->MyValidationRules(); $fillable = $package->GetMyFillableFields();
			$myRules = array(); $myFills = array();
			foreach($fillable as $name){
				if($package->$name == $request->$name){ if(isset($rules[$name])) unset($rules[$name]); }
				else {
					$myFills[$name] = $request->$name;
					if(isset($rules[$name])) $myRules[$name] = $rules[$name];
				}
			}
			if(!empty($myRules)){
				$validator = Validator::make($request->all(),$myRules);
				if($validator->fails()) { return redirect()->back()->withErrors($validator)->withInput(); }
			}
			if(!empty($myFills)){
				foreach($myFills as $N => $V) $package->$N = $V;
				$package->save();
			}
			$code = $package;
			return view("packages.edit",compact("code"))->with(array("info"=>true,"type"=>"success","text"=>"The Package: ".$request->name.", updated successfully.."));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function destroy(Package $package)
    {
			$name = $package->name; $package->active = "0"; $package->save();
			return redirect()->back()->with(array("info"=>true,"type"=>"info","text"=>"The Package: " . $name . ", deleted successfully.."));
    }
	
		public function upload($Product = NULL, $Edition = NULL, $Package = NULL){
			if(is_null($Product)){
				$Product = Product::whereActive("1")->get();
				return view("packages.upload",compact("Product"));
			} elseif(is_null($Edition)){
				$Product = Product::find($Product);
				$Edition = $Product->editions;
				return view("packages.upload",compact("Product","Edition"));
			} elseif(is_null($Package)){
				$Product = Product::find($Product);
				$Edition = $Product->load(["editions" => function($Q) use($Edition) { $Q->where("edition",$Edition); }])->editions->first();
				$Package = ProductEditionPackage::where(["product"=>$Product->code,"edition"=>$Edition->code])->with("packages")->get();
				return view("packages.upload",compact("Product","Edition","Package"));
			} else {
				$DetObj = PackageVersion::where(["product"=>$Product,"edition"=>$Edition,"package"=>$Package])->whereIn('status',['APPROVED','PENDING','AWAITING UPLOAD'])->orderBy("version_sequence","desc")->with("product","edition","package");
				if($DetObj->get()->count() > 0) $Details = $DetObj->first()->toArray();
				else {
					$Details = ProductEditionPackage::where(["product"=>$Product,"edition"=>$Edition,"package"=>$Package])->with("product","edition","package")->first()->toArray();
					$Details = array_merge($Details,["version_sequence"=>0,"major_version"=>0,"minor_version"=>0,"build_version"=>0,"revision"=>0]);
				}
				//return compact("Product","Edition","Package","Details");
				return view("packages.form",compact("Product","Edition","Package","Details"));
			}
		}
	
	public function doupload(Product $Product, Edition $Edition, Package $Package, Request $Request){
		$version_string = $Product->basename . $Edition->name . "Edition" . $Package->base_name;
		$version_numeric = $Request->major_version . "." . $Request->minor_version . "." . $Request->build_version . "." . $Request->revision;
		$ReqdName = $version_string . $version_numeric;
		$StorePath = "upload/packages/" . $Product->basename . "/" . $Edition->name . "/" . $Package->base_name;
		$PV = new PackageVersion();
		$VSO = $PV->select("version_sequence")->where(["product"=>$Product->code,"edition"=>$Edition->code,"package"=>$Package->code])->orderBy("version_sequence","desc");
		if($VSO->get()->count() < 1) { $VersionSequence = 1; }
		else { $VersionSequence = $VSO->first()->version_sequence+1; }
		if($Request->upload_over_ftp == "YES"){
			$Status = (\File::exists(storage_path() . "/app/" . $StorePath . "/" . $ReqdName . ".exe")) ? "PENDING" : "AWAITING UPLOAD";
			$File = $StorePath . "/" . $ReqdName . ".exe";
			if($Status == "AWAITING UPLOAD") $StatusText = "Record Created successfully.<br>Upload package<br><strong>" . $ReqdName . ".exe" . "</strong><br>to<br><strong>" . "storage/app/" . $StorePath . "</strong> and VERIFY UPLOAD to complete the process.";
			else $StatusText = "Uploaded file verified successfully..";
		} else {
			//if($Request->file("package")->isValid()){
				 $isValid = ProductEditionPackage::where(["product" => $Product->code, "edition" => $Edition->code, "package" => $Package->code])->get()->count();
				if($isValid){
					//if(in_array($Request->file("package")->guessExtension(),["exe"])){
						$FileName = $Request->file("package")->getClientOriginalName();
						if($FileName == $ReqdName.".exe"){
							//if(!is_dir($StorePath)) mkdir($StorePath,0777,true);
							$File = $Request->file("package")->storeAs($StorePath, $FileName);
							//if(move_uploaded_file($_FILES['package']['tmp_name'],$StorePath.'/'.$FileName)){
								//$File = $StorePath.'/'.$FileName;
								$Status = "PENDING";
								$StatusText = "File Uploaded successfully to, " . $File;
							//} else {
							//	return redirect()->back()->with(array("info"=>true,"type"=>"danger","text"=>"Error in moving uploaded file to specified folder."));
							//}
						} else {
							return redirect()->back()->with(array("info"=>true,"type"=>"danger","text"=>"Expected and Uploaded filenames are not matching."));
						}
					//} else {
						//return redirect()->back()->with(array("info"=>true,"type"=>"danger","text"=>"The file is not valid"));
					//}
				} else {
					return redirect()->back()->with(array("info"=>true,"type"=>"danger","text"=>"Product -> Edition -> Package combination is not valid."));
				}
			//} else {
			//	return redirect()->back()->with(array("info"=>true,"type"=>"danger","text"=>"Uploaded file is not valid"));
			//}			
		}
		$PV->create([
			"product" => $Product->code,
			"edition" => $Edition->code,
			"package" => $Package->code,
			"version_sequence" => $VersionSequence,
			"major_version" => $Request->major_version,
			"minor_version" => $Request->minor_version,
			"build_version" => $Request->build_version,
			"revision" => $Request->revision,
			"version_string" => $version_string,
			"version_numeric"	=>	$version_numeric,
			"build_date" => date("Y-m-d h:i:s",strtotime($Request->build_date)),
			"deploy_date" => date("Y-m-d h:i:s"),
			"change_log" => $Request->change_log,
			"file" => $File,
			"status"	=>	$Status
		]);
		return redirect()->back()->with(array("info"=>true,"type"=>"info","text"=>$StatusText));
	}
	
	public function verify(){
		$data = PackageVersion::awaiting()->with("product","edition","package")->orderBy("deploy_date","desc")->orderBy("version_sequence","desc")->get()->toArray();
		$status = "AWAITING UPLOAD";
		return view("packages.status",compact("data","status"));
	}
	
	public function doverify(Request $Request){
		$Obj = PackageVersion::awaiting()->where(["product"=>$Request->product,"edition"=>$Request->edition,"package"=>$Request->package,"version_sequence"=>$Request->sequence]);
		if($Obj->get()->count()){
			$data = $Obj->first()->toArray();
			if(\File::exists(storage_path() . "/app/" . $data["file"])){
				$Obj->update(["status"=>"PENDING"]);
				return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Verification success.. Submitted for approval."]);
			} else {
				return redirect()->back()->with(["info"=>true,"type"=>"danger","text"=>"File, <strong>storage/app/".$data["file"]."</strong>, still not existing.<br>Please upload and verify again."]);
			}
		} else {
			return redirect()->back()->with(["info"=>true,"type"=>"danger","text"=>"No packages to verify."]);
		}
	}
	
	public function approve(){
		$Obj = PackageVersion::pending()->with("product","edition","package")->orderBy("deploy_date","desc")->orderBy("version_sequence","desc");
		if($Obj->get()->count()){
			$data = $Obj->get()->toArray();
		} else {
			$data = [];
		}
		$status = "PENDING";
		return view("packages.status",compact("data","status"));
	}
	
	public function doapprove(Request $Request){
		$Obj = PackageVersion::pending()->where(["product"=>$Request->product,"edition"=>$Request->edition,"package"=>$Request->package,"version_sequence"=>$Request->sequence]);
		if($Obj->get()->count()){
			$submit = $Request->submit; //Download,Approve,Reject
			$UpdateArray = [];
			switch($submit){
				case "Approve":
					$UpdateArray["status"] = "APPROVED"; $UpdateArray["approved_date"] = date('Y-m-d H:i:s');
					break;
				case "Download":
					return response()->download(storage_path() . "/app/" . $Obj->select("file")->first()->file);
					break;
				case "Reject":
					$UpdateArray["status"] = "REJECTED"; $UpdateArray["status_reason"] = $Request->reason;
					break;
			}
			$Obj->update($UpdateArray);
			return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Action completed successfully."]);
		} else {
			return redirect()->back()->with(["info"=>true,"type"=>"danger","text"=>"No such package to verify."]);
		}
	}
	
	public function revert(){
		$status = "APPROVED";
		$Condition = PackageVersion::status($status)->select('product','edition','package',\DB::raw('MAX(version_sequence) AS version'))->groupBy('product','edition','package')->get()
			->map(function($item){ return implode('-',[$item->product,$item->edition,$item->package,$item->version]); })->toArray();
		$Obj = PackageVersion::status($status)->with("product","edition","package")->orderBy("approved_date","desc")->get()
			->keyBy(function($item){ return implode('-',[$item->product,$item->edition,$item->package,$item->version_sequence]); })
			->filter(function($item, $key) use ($Condition){ return in_array($key,$Condition); })
			->values()
			->toArray();
		$data = ($Obj)?:[];
		return view("packages.status",compact("data","status"));
	}
	
	public function dorevert(Request $request){
		$status = "APPROVED";
		$PV = PackageVersion::where(['product'=>$request->product,'edition'=>$request->edition,'package'=>$request->package,'version_sequence'=>$request->sequence,'status'=>$status]);
		$new_status = 'REVERTED'; $new_reason = $request->reason;
		$PV->update(['status'	=>	$new_status, 'status_reason'	=>	$new_reason]);
		return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Package Reverted Successfully"]);
	}
	
	public function delete(){
		$Obj = PackageVersion::whereIn('status',['AWAITING UPLOAD','PENDING'])->with("product","edition","package")->latest()->get()
			//->keyBy(function($item){ return implode('-',[$item->product,$item->edition,$item->package,$item->version_sequence]); })
			//->filter(function($item, $key) use ($Condition){ return in_array($key,$Condition); })
			//->values()
			->toArray();
		$data = ($Obj)?:[];
		$status = "DELETE";
		return view("packages.status",compact("data","status"));
	}
	
	public function dodelete(Request $request){
		PackageVersion::whereIn('status',['AWAITING UPLOAD','PENDING'])->where(['product'=>$request->product,'edition'=>$request->edition,'package'=>$request->package,'version_sequence'=>$request->sequence])->delete();
		return redirect()->back()->with(["info"=>true,"type"=>"warning","text"=>"Package Deleted Succesfully"]);
	}
	
	protected $PV;
	
	public function latest(){
		$Product = \App\Models\Product::select('code','name')->with(['Editions'	=> function($Q){
			$Q->oldest('pivot_level')->select('code','name')->with(['Packages'	=>	function($Q){
				$Q->select('code','name');
			}]);
		}])->get()->toArray();
		$this->PV = new PackageVersion();
		$Data = [];
		if(!empty($Product)) foreach($Product as $PRD){
			if(!array_key_exists($PRD['code'],$Data)) $Data[$PRD['code']] = ['name'=>$PRD['name'],'editions'=>[]];
			if(!empty($PRD['editions'])) foreach($PRD['editions'] as $EDN){
				if($EDN['pivot']['product'] == $PRD['code'] && !array_key_exists($EDN['code'],$Data[$PRD['code']]['editions'])){
					$Data[$PRD['code']]['editions'][$EDN['code']] = ['name'	=>	$EDN['name'], 'packages'	=>	[]];
					if(!empty($EDN['packages'])) foreach($EDN['packages'] as $PKG){
						if($PKG['pivot']['product'] == $PRD['code'] && $PKG['pivot']['edition'] == $EDN['code'] && !array_key_exists($PKG['code'],$Data[$PRD['code']]['editions'][$EDN['code']]['packages'])){
							$Data[$PRD['code']]['editions'][$EDN['code']]['packages'][$PKG['code']] = ['name'=>$PKG['name'],'version'=>$this->LatestVersion($PRD['code'], $EDN['code'], $PKG['code'])];
						}
					}
				}
			}
		}
		return view("packages.latest",compact("Data"));
	}
	
	private function LatestVersion($PR, $ED, $PK){
		$Result = $this->PV->whereStatus('APPROVED')->latest('version_sequence')->select('version_numeric','build_date','version_sequence')->where(['product'	=>	$PR, 'edition'	=>	$ED, 'package'	=>	$PK])->first();
		return ($Result) ? $Result->toArray() : null;
	}
	
	private function GetPackageFile($PR,$ED,$PK,$SQ){
		return PackageVersion::where(['product'	=>	$PR, 'edition'	=>	$ED, 'package'	=>	$PK, 'status'	=>	'APPROVED', 'version_sequence'	=>	$SQ])->first()->file;
	}
	
	public function download($PR,$ED,$PK,$SQ){
		$File = $this->GetPackageFile($PR,$ED,$PK,$SQ);
		//return storage_path("app/".$File);
		return response()->download(storage_path("app/".$File));
	}
	
	
	
}















