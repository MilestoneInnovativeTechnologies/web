<?php

namespace App\Http\Controllers;

use App\Models\Edition;
use Illuminate\Http\Request;
use Validator;


class EditionController extends Controller
{
		
		
		public function __construct(){
			//dd(\Auth::user());
			//$this->middleware('auth');
		}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Edition $Edition, Request $Request)
    {
			$ItemsPerPage = ($Request->items_per_page == null)? 10 : $Request->items_per_page;
			$PageNumber = ($Request->page == null)? 1 : $Request->page;
			$Skip = ($PageNumber-1) * $ItemsPerPage;
			$editions = $Edition->select("code","name","private","description_internal","description_public")->whereActive("1")->skip($Skip)->take($ItemsPerPage)->get();
			return view("editions.index",compact("editions","ItemsPerPage","PageNumber"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
			return view('editions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
			$editions = new Edition();
			//$this->validate($request,$products->MyValidationRules());
			$Rules = $editions->MyValidationRules();
			$validator = Validator::make($request->all(),$Rules);
			if($validator->fails()) { return redirect()->back()->withErrors($validator)->withInput(); }
			$Private = ((isset($request->private) && $request->private == "YES") ? "YES" : "NO");
			
			$editions->create(array(
				"code"	=>	$editions->NextCode(),
				"name"	=>	$request->name,
				"private"	=>	$Private,
				"description_public"	=>	$request->description,
				"description_internal"	=>	$request->description_internal,	
			));
			
			$IndexData = array("info"=>true,"type"=>"success","text"=>"The new Edition: ".$request->name.", Added successfully..","editions"=>$editions->take(10)->get(),"ItemsPerPage"=>10,"PageNumber"=>1);
			return view("editions.index",$IndexData);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Edition  $edition
     * @return \Illuminate\Http\Response
     */
    public function show(Edition $edition)
    {
			$code = $edition;
			return view("editions.show",compact("code"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Edition  $edition
     * @return \Illuminate\Http\Response
     */
    public function edit(Edition $edition)
    {
			$code = $edition;
			return view("editions.edit",compact("code"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Edition  $edition
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Edition $edition)
    {
			$rules = $edition->MyValidationRules(); $fillable = $edition->GetMyFillableFields();
			$request->private = ((isset($request->private) && $request->private == "YES") ? "YES" : "NO");
			$myRules = array(); $myFills = array();
			foreach($fillable as $name){
				if($edition->$name == $request->$name){ if(isset($rules[$name])) unset($rules[$name]); }
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
				foreach($myFills as $N => $V) $edition->$N = $V;
				$edition->save();
			}
			$code = $edition;
			return view("editions.edit",compact("code"))->with(array("info"=>true,"type"=>"success","text"=>"The Edition: ".$request->name.", updated successfully.."));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Edition  $edition
     * @return \Illuminate\Http\Response
     */
    public function destroy(Edition $edition)
    {
			$name = $edition->name; $edition->active = "0"; $edition->save();
			return redirect()->back()->with(array("info"=>true,"type"=>"info","text"=>"The Edition: " . $name . ", deleted successfully.."));
    }
}
