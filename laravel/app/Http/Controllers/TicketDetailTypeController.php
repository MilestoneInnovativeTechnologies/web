<?php

namespace App\Http\Controllers;

use App\Models\TicketDetailType;
use Illuminate\Http\Request;

use Validator;

class TicketDetailTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('tdt.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
				$Data["code"] = (new TicketDetailType())->NewCode();
        return view('tdt.form',compact("Data"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
			$Data = new TicketDetailType();
			$Validator = Validator::make($request->all(),$Data->ValidationRules(),$Data->ValidationMessages());
			if($Validator->fails()) return redirect()->back()->withErrors($Validator)->withInput();
			$Fields = ['code','name','description'];
			$Extra = ['created_by'	=>	$request->user()->partner];
      return ($Data->create(array_merge($request->only($Fields),$Extra))) ? redirect()->route("tdt.index") : redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in creating new ticket detail type."]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\App\Models\TicketDetailType  $ticketDetailType
     * @return \Illuminate\Http\Response
     */
    public function show($ticketDetailType)
    {
        $Data = TicketDetailType::find($ticketDetailType);
				return view('tdt.show',compact("Data"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\App\Models\TicketDetailType  $ticketDetailType
     * @return \Illuminate\Http\Response
     */
    public function edit($ticketDetailType)
    {
        $Data = TicketDetailType::find($ticketDetailType);
				$Update = true;
				return view("tdt.form",compact("Data","Update"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\App\Models\TicketDetailType  $ticketDetailType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $ticketDetailType)
    {
			$Fields = ['code','name','description'];
			$Req = $request->only($Fields);
			$TDT = TicketDetailType::find($ticketDetailType);
			$Rules = $TDT->ValidationRules(); $Msgs = $TDT->ValidationMessages();
			foreach($Fields as $Field)
				if($Req[$Field] == $TDT->$Field)
					unset($Rules[$Field],$Req[$Field]);
			$Validator = Validator::make($Req,$Rules,$Msgs);
			if($Validator->fails()) return redirect()->back()->withErrors($Validator)->withInput();
			$TDT->update($Req);
			return redirect()->route('tdt.edit',['ticketDetailType'	=>	$request->code])->withInput()->with(["info"=>true,"type"=>"success","text"=>"Updated Successfully."]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\App\Models\TicketDetailType  $ticketDetailType
     * @return \Illuminate\Http\Response
     */
    public function destroy($ticketDetailType)
    {
        //
    }
    public function delete($ticketDetailType)
    {
        $TDT = TicketDetailType::find($ticketDetailType);
				$TDT->update(["status"	=> "INACTIVE"]);
				return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Status of " . $TDT->name . ", changed to INACTIVE."]);
    }
    public function undelete($ticketDetailType)
    {
        $TDT = TicketDetailType::find($ticketDetailType);
				$TDT->update(["status"	=> "ACTIVE"]);
				return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Status of " . $TDT->name . ", changed to ACTIVE."]);
    }
}
