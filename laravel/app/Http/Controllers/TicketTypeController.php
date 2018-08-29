<?php

namespace App\Http\Controllers;

use App\Models\TicketType;
use Illuminate\Http\Request;

use Validator;

class TicketTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('stt.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
				$Data["code"] = (new TicketType())->NewCode();
        return view('stt.form',compact("Data"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
			$Data = new TicketType();
			$Validator = Validator::make($request->all(),$Data->ValidationRules(),$Data->ValidationMessages());
			if($Validator->fails()) return redirect()->back()->withErrors($Validator)->withInput();
			$Fields = ['code','name','description'];
			$Extra = ['created_by'	=>	$request->user()->partner];
      return ($Data->create(array_merge($request->only($Fields),$Extra))) ? redirect()->route("stt.index") : redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in creating new ticket type."]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\App\Models\TicketType  $ticketType
     * @return \Illuminate\Http\Response
     */
    public function show($ticketType)
    {
        $Data = TicketType::find($ticketType);
				return view('stt.show',compact("Data"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\App\Models\TicketType  $ticketType
     * @return \Illuminate\Http\Response
     */
    public function edit($ticketType)
    {
        $Data = TicketType::find($ticketType);
				$Update = true;
				return view("stt.form",compact("Data","Update"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\App\Models\TicketType  $ticketType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $ticketType)
    {
			$Fields = ['code','name','description'];
			$Req = $request->only($Fields);
			$STT = TicketType::find($ticketType);
			$Rules = $STT->ValidationRules(); $Msgs = $STT->ValidationMessages();
			foreach($Fields as $Field)
				if($Req[$Field] == $STT->$Field)
					unset($Rules[$Field],$Req[$Field]);
			$Validator = Validator::make($Req,$Rules,$Msgs);
			if($Validator->fails()) return redirect()->back()->withErrors($Validator)->withInput();
			$STT->update($Req);
			return redirect()->route('stt.edit',['ticketType'	=>	$request->code])->withInput()->with(["info"=>true,"type"=>"success","text"=>"Updated Successfully."]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\App\Models\TicketType  $ticketType
     * @return \Illuminate\Http\Response
     */
    public function destroy(TicketType $ticketType)
    {
        //
    }
    public function delete($ticketType)
    {
        $STT = TicketType::find($ticketType);
				$STT->update(["status"	=> "INACTIVE"]);
				return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Status of " . $STT->name . ", changed to INACTIVE."]);
    }
    public function undelete($ticketType)
    {
        $STT = TicketType::find($ticketType);
				$STT->update(["status"	=> "ACTIVE"]);
				return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Status of " . $STT->name . ", changed to ACTIVE."]);
    }
}
