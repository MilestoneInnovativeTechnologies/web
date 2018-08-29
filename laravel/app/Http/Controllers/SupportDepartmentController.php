<?php

namespace App\Http\Controllers;

use App\Models\SupportDepartment;
use Illuminate\Http\Request;

use Validator;

class SupportDepartmentController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
			//return \App\Models\SupportDepartment::count();
        return view('sd.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
				$Data["code"] = (new SupportDepartment())->NewCode();
        return view('sd.form',compact("Data"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
			$Data = new SupportDepartment();
			$Validator = Validator::make($request->all(),$Data->ValidationRules(),$Data->ValidationMessages());
			if($Validator->fails()) return redirect()->back()->withErrors($Validator)->withInput();
			$Fields = ['code','name','description'];
			$Extra = ['created_by'	=>	$request->user()->partner];
      return ($Data->create(array_merge($request->only($Fields),$Extra))) ? redirect()->route("sd.index") : redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in creating new department."]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\App\Models\SupportDepartment  $supportDepartment
     * @return \Illuminate\Http\Response
     */
    public function show($supportDepartment)
    {
        $Data = SupportDepartment::find($supportDepartment);
				return view('sd.show',compact("Data"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\App\Models\SupportDepartment  $supportDepartment
     * @return \Illuminate\Http\Response
     */
    public function edit($supportDepartment)
    {
        $Data = SupportDepartment::find($supportDepartment);
				$Update = true;
				return view("sd.form",compact("Data","Update"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\App\Models\SupportDepartment  $supportDepartment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $supportDepartment)
    {
			$Fields = ['code','name','description'];
			$Req = $request->only($Fields);
			$SD = SupportDepartment::find($supportDepartment);
			$Rules = $SD->ValidationRules(); $Msgs = $SD->ValidationMessages();
			foreach($Fields as $Field)
				if($Req[$Field] == $SD->$Field)
					unset($Rules[$Field],$Req[$Field]);
			$Validator = Validator::make($Req,$Rules,$Msgs);
			if($Validator->fails()) return redirect()->back()->withErrors($Validator)->withInput();
			$SD->update($Req);
			return redirect()->route('sd.edit',['supportDepartment'	=>	$request->code])->withInput()->with(["info"=>true,"type"=>"success","text"=>"Updated Successfully."]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\App\Models\SupportDepartment  $supportDepartment
     * @return \Illuminate\Http\Response
     */
    public function destroy(SupportDepartment $supportDepartment)
    {
        //
    }
    public function delete($supportDepartment)
    {
        $SD = SupportDepartment::find($supportDepartment);
				$SD->update(["status"	=> "INACTIVE"]);
				return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Status of " . $SD->name . ", changed to INACTIVE."]);
    }
    public function undelete($supportDepartment)
    {
        $SD = SupportDepartment::find($supportDepartment);
				$SD->update(["status"	=> "ACTIVE"]);
				return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Status of " . $SD->name . ", changed to ACTIVE."]);
    }
}
