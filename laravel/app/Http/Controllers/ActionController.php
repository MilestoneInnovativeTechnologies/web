<?php

namespace App\Http\Controllers;

use App\Models\Action;
use Illuminate\Http\Request;
use Validator;

class ActionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Items = Action::all();
				return view("action.index",compact("Items"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
			return view("action.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $Action = New Action();
			$Validator = Validator::make($request->all(),$Action->ValidationRules(),$Action->ValidationMessages());
			if($Validator->fails()) return redirect()->back()->withErrors($Validator)->withInput();
			$Action->create($request->all());
			return redirect()->route("action.index")->with(["info"=>true,"type"=>"success","text"=>$request->displayname." Created Successfully"]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Action  $action
     * @return \Illuminate\Http\Response
     */
    public function show(Action $Action)
    {
        return view("action.show",compact("Action"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Action  $action
     * @return \Illuminate\Http\Response
     */
    public function edit(Action $action)
    {
			$Item = $action; $update = true;
			return view("action.create",compact("Item"))->with(["update"=>true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Action  $action
     * @return \Illuminate\Http\Response
     */
    public function update(Request $Request, Action $Action)
    {
			//return [$Action->FillableFields(),$Action,$Request->all()];
			$UpdateArray = []; $Rules = Action::ValidationRules(); $MyRules = [];
			foreach($Action->FillableFields() as $Field){
				if(($Request->$Field != $Action->$Field)){
					$Action->$Field = $UpdateArray[$Field] = $Request->$Field;
					if(isset($Rules[$Field])) $MyRules[$Field] = $Rules[$Field];
				}
			}
			if(empty($UpdateArray)) return redirect()->back()->with(["info"=>true,"type"=>"info","text"=>"No fields to update."]);
			$Validator = Validator::make($UpdateArray,$MyRules,Action::ValidationMessages());
			if($Validator->fails()) return redirect()->back()->withErrors($Validator);
			if($Action->save()) return redirect()->route('action.index')->with(["info"=>true,"type"=>"info","text"=>"The Action: " . $Action->displayname . ", updated successfully"]);
			return view("action.error");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Action  $action
     * @return \Illuminate\Http\Response
     */
    public function destroy(Action $action)
    {
    	return redirect()->route("action.index")->with(["info"=>true,"type"=>"success","text"=>"Actions are not destroyable"]);
    }
}
