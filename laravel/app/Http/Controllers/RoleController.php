<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Resource;
use App\Models\Action;

use Illuminate\Http\Request;
use Validator;
use Cookie;

class RoleController extends Controller
{
		
		public function __construct(){
			
			$this->middleware(function($request, $next){
				
				$Role = Role::find($request->segment(2));
				if($Role->status != "ACTIVE") return redirect()->route("role.error");
				return $next($request);
				
			})->except(["index","store","create","select","denied"]);
			
		}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
			$Roles = Role::whereStatus("ACTIVE")->get();//->toArray();
			return view("role.index",compact("Roles"));
			
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
			return view("role.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $Role = New Role();
			$Validator = Validator::make($request->all(),$Role->ValidationRules(),$Role->ValidationMessages());
			if($Validator->fails()) return redirect()->back()->withErrors($Validator)->withInput();
			$Role->create(array_merge($request->all(),["code"=>$Role->NextCode()]));
			return redirect()->route("role.index")->with(["info"=>true,"type"=>"success","text"=>$request->displayname." Added Successfully"]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $Role)
    {
      return view("role.show",compact("Role"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $Role)
    {
      return view("role.create",compact("Role"))->with(["update"=>true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $Request, Role $Role)
    {
			$UpdateArray = []; $Rules = Role::ValidationRules(); $MyRules = [];
			$Fields = ["name","displayname","description"];
			foreach($Fields as $Field){
				if(($Request->$Field != $Role->$Field)){
					$Role->$Field = $UpdateArray[$Field] = $Request->$Field;
					if(isset($Rules[$Field])) $MyRules[$Field] = $Rules[$Field];
				}
			}
			if(empty($UpdateArray)) return redirect()->back()->with(["info"=>true,"type"=>"info","text"=>"No fields to update."]);
			$Validator = Validator::make($UpdateArray,$MyRules,Role::ValidationMessages());
			if($Validator->fails()) return redirect()->back()->withErrors($Validator);
			if($Role->status == "ACTIVE" && $Role->save()) return redirect()->route('role.index')->with(["info"=>true,"type"=>"info","text"=>"The role: " . $Role->displayname . ", updated successfully"]);
			return view("role.error",["item"	=>	"role"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $Role)
    {
			$name = $Role->displayname; $Role->status = "INACTIVE"; $Role->save();
			return redirect()->back()->with(["info"=>true,"type"=>"info","text"=>"The Role: " . $name . ", deleted successfully.."]);
		}
	
	public function resource(Role $Role){
		$Resources = Resource::select("code","name","displayname","action")->whereStatus("active")->get();
		$RoleResources = $Role->resources()->get()->toArray();
		$Actions = Action::select("id","name","displayname")->get()->toArray();
		return view("role.resource",compact("Role","Resources","RoleResources","Actions"));
	}
	
	public function resourceupdate(Role $Role, Request $Request){
		$AttachArray = []; $Resources = $Request->resources; $ActObj = ($Request->actions)?:[];
		if(!empty($Resources)){
			foreach($Resources as $RoleCode){
				$ActArray = array_key_exists($RoleCode,$ActObj) ? $ActObj[$RoleCode] : ["1"];
				$AttachArray[$RoleCode] = ['action' => "0." . implode("",array_replace(array_fill(1,max($ActArray),0),array_fill_keys($ActArray,1)))];
			}
		}
		if($Role->resources()->sync($AttachArray)) return redirect()->route('role.resource',["role"=>$Role->code])->with(["info"=>true,"type"=>"success","text"=>"Resources for the Role: " . $Role->displayname . ", updated successfully"]);
		return redirect()->route('role.error');
	}

	public function select(Request $Request, $role = NULL){
		$user = $Request->user();
		if($role && ($user->roles->find($role)->code == $role)){
			session()->put("_role",$role); session()->put("_rolename",$user->roles->find($role)->name);
			$_company = ($user->roles->find($role)->name == "company")?1:0;
			session()->put("_company",$_company);
			
			$RedirectURL = session("_after_roleselect");
			session()->forget("_after_roleselect");
			
			$uuid = implode("|",[$role,str_random(15),$_company,$user->roles->find($role)->name]);
			$user->update(["api_token"=>$uuid]);
			Cookie::queue("api_token",$uuid);
			
			event(new \App\Events\LogUserLogin($user,$user->roles->find($role)->name));
			
			if($RedirectURL) return redirect($RedirectURL);
			return redirect()->route("dashboard");
		} else {
			if($user->roles()->count() == 1) return $this->select($Request, $user->roles->first()->code);
			session()->put("_roles",$user->roles()->count());
			$Roles = $user->roles;
			return view("role.select",compact("Roles"));
		}
	}
	
	public function denied(){
		return view("role.denied");
	}








}
