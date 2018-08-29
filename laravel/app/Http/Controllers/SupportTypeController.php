<?php

namespace App\Http\Controllers;

use App\Models\SupportType;
use Illuminate\Http\Request;

use Validator;

class SupportTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('st.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
				$Data["code"] = (new SupportType())->NewCode();
        return view('st.form',compact("Data"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
			$Data = new SupportType();
			$Validator = Validator::make($request->all(),$Data->ValidationRules(),$Data->ValidationMessages());
			if($Validator->fails()) return redirect()->back()->withErrors($Validator)->withInput();
			$Fields = ['code','name','description'];
			$Extra = ['created_by'	=>	$request->user()->partner];
      return ($Data->create(array_merge($request->only($Fields),$Extra))) ? redirect()->route("st.index") : redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in creating new support type."]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\App\Models\SupportType  $supportType
     * @return \Illuminate\Http\Response
     */
    public function show($supportType)
    {
        $Data = SupportType::find($supportType);
				return view('st.show',compact("Data"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\App\Models\SupportType  $supportType
     * @return \Illuminate\Http\Response
     */
    public function edit($supportType)
    {
        $Data = SupportType::find($supportType);
				$Update = true;
				return view("st.form",compact("Data","Update"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\App\Models\SupportType  $supportType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $supportType)
    {
			$Fields = ['code','name','description'];
			$Req = $request->only($Fields);
			$STT = SupportType::find($supportType);
			$Rules = $STT->ValidationRules(); $Msgs = $STT->ValidationMessages();
			foreach($Fields as $Field)
				if($Req[$Field] == $STT->$Field)
					unset($Rules[$Field],$Req[$Field]);
			$Validator = Validator::make($Req,$Rules,$Msgs);
			if($Validator->fails()) return redirect()->back()->withErrors($Validator)->withInput();
			$STT->update($Req);
			return redirect()->route('st.edit',['supportType'	=>	$request->code])->withInput()->with(["info"=>true,"type"=>"success","text"=>"Updated Successfully."]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\App\Models\SupportType  $supportType
     * @return \Illuminate\Http\Response
     */
    public function destroy($supportType)
    {
        //
    }
    public function delete($supportType)
    {
        $STT = SupportType::find($supportType);
				$STT->update(["status"	=> "INACTIVE"]);
				return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Status of " . $STT->name . ", changed to INACTIVE."]);
    }
    public function undelete($supportType)
    {
        $STT = SupportType::find($supportType);
				$STT->update(["status"	=> "ACTIVE"]);
				return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Status of " . $STT->name . ", changed to ACTIVE."]);
    }
}
