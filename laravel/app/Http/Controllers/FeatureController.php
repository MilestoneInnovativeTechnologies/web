<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use Illuminate\Http\Request;
use Validator;
use DB;

class FeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Feature $Feature, Request $Request)
    {
			$ItemsPerPage = ($Request->items_per_page == null)? 10 : $Request->items_per_page;
			$PageNumber = ($Request->page == null)? 1 : $Request->page;
			$Skip = ($PageNumber-1) * $ItemsPerPage;
			$features = $Feature->select("id","name", "category","value_type","description_internal","description_public")->whereActive("1")->skip($Skip)->take($ItemsPerPage)->get();
			return view("features.index",compact("features","ItemsPerPage","PageNumber"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
			$Categories = array_map(function($C){ return $C->name; },DB::select("SELECT * FROM feature_category"));
			return view('features.create',compact("Categories"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
			$features = new Feature();
			$Rules = $features->MyValidationRules();
			if(!isset($request->category)) $request->category = null;
			$validator = Validator::make($request->all(),$Rules);
			if($validator->fails()) { return redirect()->back()->withErrors($validator)->withInput(); }
			
			$OPTIONS = array();
			if($request->value_type == "OPTION" || $request->value_type == "MULTISELECT"){
				if(empty($request->option)) { $request->session()->flash("info",true); $request->session()->flash("status","danger"); $request->session()->flash("text","For Value type ".$request->value_type.", should provide atleast 1 option."); return redirect()->back()->withInput(); }
				foreach($request->option as $iter => $option){
					if(trim($option) == "") continue;
					$OPTIONS[] = array("option"=>$option,"order"=>count($OPTIONS)+1);
				}
				if(count($OPTIONS) < 1) { session(array("info"=>true,"status"=>"danger","text"=>"For Value type ".$request->value_type.", must provide atleast 1 valid option.")); return redirect()->back()->withInput(); }
			}
			if($request->category == "-1") {
				if(isset($request->new_category) && !is_null($request->new_category)) {
					DB::insert("INSERT INTO feature_category (name) VALUES (?)",[$request->new_category]);
					$request->category = $request->new_category;
				}
			}

			$features->create(array(
				"name"	=>	$request->name,
				"category"	=> $request->category,
				"value_type"	=>	$request->value_type,
				"description_public"	=>	$request->description,
				"description_internal"	=>	$request->description_internal,	
			))->options()->createMany($OPTIONS);
			
			$IndexData = array("info"=>true,"type"=>"success","text"=>"Feature details added successfully..","features"=>$features->take(50)->get(),"ItemsPerPage"=>50,"PageNumber"=>1);
			return view("features.index",$IndexData);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Feature  $feature
     * @return \Illuminate\Http\Response
     */
    public function show(Feature $feature)
    {
			if(in_array($feature->value_type,array("MULTISELECT","OPTION"))){
				$feature->load(array("options"=>function($Query){
					$Query->orderBy("order","asc");
				}));
			}
			$code = $feature;
			return view("features.show",compact("code"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Feature  $feature
     * @return \Illuminate\Http\Response
     */
    public function edit(Feature $feature)
    {
			if(in_array($feature->value_type,array("MULTISELECT","OPTION"))){
				$feature->load(array("options"=>function($query){
					$query->orderBy("order","asc");
				}));
			}
			$code = $feature;
			$Categories = array_map(function($C){ return $C->name; },DB::select("SELECT * FROM feature_category"));
			return view("features.edit",compact("code","Categories"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Feature  $feature
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Feature $feature)
    {
			$rules = $feature->MyValidationRules(); $fillable = $feature->GetMyFillableFields();
			$myRules = array(); $myFills = array();
			if(!isset($request->category)) $request->category = null;
			if($request->category == "-1") {
				if(isset($request->new_category) && !is_null($request->new_category)) {
					DB::insert("INSERT INTO feature_category (name) VALUES (?)",[$request->new_category]);
					$request->category = $request->new_category;
				}
			}
			foreach($fillable as $name){
				if($feature->$name == $request->$name){ if(isset($rules[$name])) unset($rules[$name]); }
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
				foreach($myFills as $N => $V) $feature->$N = $V;
				$feature->save();
			}
			
			$OPTIONS = array();
			if($request->value_type == "OPTION" || $request->value_type == "MULTISELECT"){
				if(empty($request->option)) { $request->session()->flash("info",true); $request->session()->flash("status","danger"); $request->session()->flash("text","For Value type ".$request->value_type.", should provide atleast 1 option."); return redirect()->back()->withInput(); }
				foreach($request->option as $iter => $option){
					if(trim($option) == "") continue;
					$OPTIONS[] = array("option"=>$option,"order"=>count($OPTIONS)+1);
				}
				if(count($OPTIONS) < 1) { session(array("info"=>true,"status"=>"danger","text"=>"For Value type ".$request->value_type.", must provide atleast 1 valid option.")); return redirect()->back()->withInput(); }
			}
			$feature->options()->delete();
			$feature->options()->createMany($OPTIONS);
			$code = $feature;
			$Categories = array_map(function($C){ return $C->name; },DB::select("SELECT * FROM feature_category"));
			return view("features.edit",compact("code","Categories"))->with(array("info"=>true,"type"=>"success","text"=>"The Feature: ".$request->name.", updated successfully.."));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Feature  $feature
     * @return \Illuminate\Http\Response
     */
    public function destroy(Feature $feature)
    {
			$name = $feature->name; $feature->active = "0"; $feature->save();
			return redirect()->back()->with(array("info"=>true,"type"=>"info","text"=>"The Feature: " . $name . ", deleted successfully.."));
    }
}
