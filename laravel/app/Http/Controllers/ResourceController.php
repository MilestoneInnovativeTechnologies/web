<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\Action;
use Validator;

class ResourceController extends Controller
{

		
		public function __construct(){
			
			$this->middleware(function($request, $next){
				
				$Resource = Resource::find($request->segment(2));
				if($Resource->status != "ACTIVE") return redirect()->route("resource.error");
				return $next($request);
				
			})->except(["index","store","create"]);
			
		}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
				$Items = Resource::whereStatus("ACTIVE")->get()->toArray();
				$Actions = Action::all()->toArray();
        return view('resource.index',compact("Items","Actions"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
				$Actions = Action::all()->toArray();
        return view("resource.create",compact("Actions"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
			$Actions = ($request->actions)?:[1];
			$Action = "0." . implode("",array_replace(array_fill(1,max($Actions),0),array_fill_keys($Actions,1)));
      $Resource = New Resource();
			$Validator = Validator::make($request->all(),$Resource->ValidationRules(),$Resource->ValidationMessages());
			if($Validator->fails()) return redirect()->back()->withErrors($Validator)->withInput();
			$Resource->create(array_merge($request->all(),["code"=>$Resource->NextCode(),"action"=>$Action]));
			return redirect()->route("resource.index")->with(["info"=>true,"type"=>"success","text"=>$request->displayname." Created Successfully"]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Resource  $resource
     * @return \Illuminate\Http\Response
     */
    public function show(Resource $Resource)
    {
				$Actions = Action::all()->toArray();
        return view('resource.show',compact("Resource","Actions"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Resource  $resource
     * @return \Illuminate\Http\Response
     */
    public function edit(Resource $resource)
    {
				$Item = $resource; $Actions = Action::all()->toArray();
				$Item["actions"] = array_keys(str_split(str_replace(".","",$this->floattostr ( $Item->action ))),"1");
        return view("resource.create",compact("Item","Actions"))->with(["update"=>true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Resource  $resource
     * @return \Illuminate\Http\Response
     */
    public function update(Request $Request, Resource $Resource)
    {
			$Actions = ($Request->actions)?:[1];
			$Action = "0." . implode("",array_replace(array_fill(1,max($Actions),0),array_fill_keys($Actions,1)));
			$Request->action = $Action;
			$UpdateArray = []; $Rules = Resource::ValidationRules(); $MyRules = [];
			foreach($Resource->FillableFields() as $Field){
				if($Field == "code") continue;
				if($Request->$Field != $Resource->$Field){
					$Resource->$Field = $UpdateArray[$Field] = $Request->$Field;
					if(isset($Rules[$Field])) $MyRules[$Field] = $Rules[$Field];
				}
			}
			if(empty($UpdateArray)) return redirect()->back()->with(["info"=>true,"type"=>"info","text"=>"No fields to update."]);
			$Validator = Validator::make($UpdateArray,$MyRules,Resource::ValidationMessages());
			if($Validator->fails()) return redirect()->back()->withErrors($Validator);
			if($Resource->status == "ACTIVE" && $Resource->save()) return redirect()->route('resource.index')->with(["info"=>true,"type"=>"info","text"=>"The Resource: " . $Resource->displayname . ", updated successfully"]);
			return view("resource.error");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Resource  $resource
     * @return \Illuminate\Http\Response
     */
    public function destroy(Resource $resource)
    {
        $Name = $resource->displayname; $resource->status = "INACTIVE"; $resource->save();
				return redirect()->route("resource.index")->with(["info"=>true,"type"=>"info","text"=>"The Resource: " . $Name . ", deleted successfully"]);
    }
	
		public static function floattostr( $val ){
			preg_match( "#^([\+\-]|)([0-9]*)(\.([0-9]*?)|)(0*)$#", trim($val), $o );
			return $o[1].sprintf('%d',$o[2]).($o[3]!='.'?$o[3]:'');			
		}
	
	public function role(Resource $Resource){
		$Actions = Action::select("id","name","displayname")->get()->toArray();
		$Roles = Role::select("code","name","displayname")->whereStatus("ACTIVE")->get();
		$ResourceRoles = $Resource->roles()->get();
		return view('resource.role',compact("Resource","ResourceRoles","Roles","Actions"));
		return [$Resource, $Actions, $Roles, $ResourceRoles];
	}
	
	public function roleupdate(Resource $Resource, Request $Request){
		$Roles = $Request->roles; $AttachArray = []; $ActObj = ($Request->actions)?:[];
		if(!empty($Roles)){
			foreach($Roles as $Role){
				$ActArray = array_key_exists($Role,$ActObj) ? $ActObj[$Role] : ["1"];
				$AttachArray[$Role] = ['action' => "0." . implode("",array_replace(array_fill(1,max($ActArray),0),array_fill_keys($ActArray,1)))];
			}
		}
		if($Resource->roles()->sync($AttachArray)) return redirect()->route('resource.role',["resource"=>$Resource->code])->with(["info"=>true,"type"=>"success","text"=>"Roles for the Resource: " . $Resource->displayname . ", updated successfully"]);
		return redirect()->route('resource.error');
	}
	
}
