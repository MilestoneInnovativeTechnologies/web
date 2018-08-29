<?php

namespace App\Http\Controllers;

use App\Models\PriceList;
use Illuminate\Http\Request;
use Validator;

class PriceListController extends Controller
{
	
		
		public function __construct(){
			
			$this->middleware(function($request, $next){
				$param = "pricelist";
				if($request->route($param)->status == "ACTIVE") return $next($request);
				return redirect()->route($param.".error");
			})->only(["show","edit","update","destroy"]);
			
		}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("pricelist.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
			$PL = new PriceList();
			$Code = $PL->nextCode();
				$Products = \App\Models\Product::select("code","name","private")->with(["editions" => function($q){
					$q->select("code","name","private");
				}])->whereActive(1)->get()->groupBy("code")->map(function($item, $key){
					$editions = $item[0]->editions->pluck("private","code");
					return ["name"=>$item[0]->name,"private"=>$item[0]->private,"editions"=>$editions,"code"=>$item[0]->code];
				});
				$Editions = \App\Models\Edition::select("code","name")->whereActive(1)->get()->pluck("name","code");
				//return compact("Products","Editions");
        return view("pricelist.create",compact("Products","Editions","Code"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
			$Validate = Validator::make($request->all(),$this->CreateRulesMessages()[0],$this->CreateRulesMessages()[1]);
			if($Validate->fails())	return redirect()->back()->withErrors($Validate)->withInput();
			//$PL = new PriceList();
			$request->merge(["created_by" => $request->user()->partner]);
			//$NewPL = $PL->create($request->all());
			$NewPL = PriceList::create($request->all());
			if(!$NewPL) return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Some error in creating new Price List, Please try again later."]);
			$DetailsArray = $this->Request2DetailArray($request,["product","edition","mop","price","mrp","currency"]);
			return ($NewPL->details()->createMany($DetailsArray)) ? (redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Price List, ".$request->name." created successfully."])) : (redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Some error in adding details of Price List, Please try again later."]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PriceList  $priceList
     * @return \Illuminate\Http\Response
     */
    public function show(PriceList $pricelist)
    {
        $Details = $pricelist->with("details","details.product","details.edition")->whereCode($pricelist->code)->get()->map(function($item,$key){
					return ["code"	=> $item->code,"name"	=> $item->name,"description"	=> $item->description,"items"	=>	$item->details->map(function($detail,$key){
						return ["mop"	=>	$detail->mop,"price"	=>	$detail->price,"mrp"	=>	$detail->mrp,"currency"	=>	$detail->currency, "product"	=>	$detail->product()->first()->name, "edition"	=>	$detail->edition()->first()->name];
					})];
				})->first();
				return view("pricelist.view",compact("Details"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PriceList  $priceList
     * @return \Illuminate\Http\Response
     */
    public function edit(PriceList $pricelist)
    {
        $Details = $pricelist->with("details","details.product","details.edition")->whereCode($pricelist->code)->get()->map(function($item,$key){
					return ["code"	=> $item->code,"name"	=> $item->name,"description"	=> $item->description,"items"	=>	$item->details->map(function($detail,$key){
						return ["mop"	=>	$detail->mop,"price"	=>	$detail->price,"mrp"	=>	$detail->mrp,"currency"	=>	$detail->currency, "product"	=>	$detail->product()->first()->code, "edition"	=>	$detail->edition()->first()->code];
					})];
				})->first();
				$Update = true;
				$Products = \App\Models\Product::select("code","name","private")->with(["editions" => function($q){
					$q->select("code","name","private");
				}])->whereActive(1)->get()->groupBy("code")->map(function($item, $key){
					$editions = $item[0]->editions->pluck("private","code");
					return ["name"=>$item[0]->name,"private"=>$item[0]->private,"editions"=>$editions,"code"=>$item[0]->code];
				});
				$Editions = \App\Models\Edition::select("code","name")->whereActive(1)->get()->pluck("name","code");
				$Code = $pricelist->code;
			//return compact("Details","Update","Products","Editions","Code");
				return view("pricelist.create",compact("Details","Update","Products","Editions","Code"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PriceList  $priceList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PriceList $pricelist)
    {
			$RM = $this->CreateRulesMessages(); $Rules = $RM[0]; $Messages = $RM[1];
			if($request->code == $pricelist->code) unset($Rules["code"]);
			$Validate = Validator::make($request->all(),$Rules,$Messages);
			if($Validate->fails()) return redirect()->back()->withInput()->withErrors($Validate);
			if(!$pricelist->update($request->all())) return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in updating Price List. Please try again later."]);
			$detailArray = $this->Request2DetailArray($request,["product","edition","mop","price","mrp","currency"]);
			if($pricelist->details()->delete() === false) return redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in updating Price List Details (Pre Update process failed). Please try again later."]);
			return ($pricelist->details()->createMany($detailArray)) ? (redirect()->route('pricelist.edit',["pricelist"=>$pricelist->code])->with(["info"=>true,"type"=>"success","text"=>"Price List, ".$request->name." updated successfully."])) : (redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in updating Price List Details. Please try again later."]));			
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PriceList  $priceList
     * @return \Illuminate\Http\Response
     */
    public function destroy(PriceList $pricelist)
    {
			$name = $pricelist->name; $pricelist->update(["status"=>"INACTIVE"]);
			return redirect()->back()->with(["info"=>true,"type"=>"info","text"=>"The Price List, " . $name . ", deleted successfully.."]);
    }
	
	static function CreateRulesMessages(){
		return [
			["code"	=>	"required|unique:price_lists,code",
			 "name"	=>	"required",
			 "product.*"	=>	"required",
			 "edition.*"	=>	"required",
			 "mop.*"	=>	"required",
			 "price.*"	=>	"required",
			 "mrp.*"	=>	"required"],
			["code.required"	=> "Price list code is required field",
			 "code.unique"	=>	"The code entered is already there in records.",
			 "name.required"	=>	"Name is Mandatory field.",
			 "product.*.required"	=>	"Products must be selected in all lines",
			 "edition.*.required"	=>	"Editions must be selected in all lines",
			 "currency.*.required"	=>	"Currencies must be entered in all lines"]
		];
	}
	
	static function Request2DetailArray($request, $fields){
		$DetailsArray = [];
		foreach($fields as $field){
			foreach($request->$field as $k => $v){
				$DetailsArray[$k][$field] = $v;
			}
		}
		return $DetailsArray;
			
		$MyVals[$Field] = $request->$Field[$k];
		foreach($request->product as $k => $v){
			$MyVals = [];
			foreach($fields as $Field) $MyVals[$Field] = $request->$Field[$k];
			$DetailsArray[] = $MyVals;
		}
		return $DetailsArray;
	}
	
	function apigetall(){
		return PriceList::select("code","name")->whereStatus("ACTIVE")->get()->map(function($item, $key){
			return [$item->code, $item->name];
		});
	}
	
}
