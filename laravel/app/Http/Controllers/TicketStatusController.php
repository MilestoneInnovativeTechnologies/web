<?php

namespace App\Http\Controllers;

use App\Models\TicketStatus;
use Illuminate\Http\Request;

use Validator;

class TicketStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('ts.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
				$Data["code"] = (new TicketStatus())->NewCode();
				$TS = TicketStatus::whereStatus("ACTIVE")->pluck('name','code')->toArray();
				$PS = [''=>'(initial status)'] + $TS;
				$SS = [''=>'(none)'] + $TS;
				$MS = ['NEW','INPROCESS','COMPLETED'];
        return view('ts.form',compact("Data","PS","SS","MS"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
			$Data = new TicketStatus();
			$Validator = Validator::make($request->all(),$Data->ValidationRules(),$Data->ValidationMessages());
			if($Validator->fails()) return redirect()->back()->withErrors($Validator)->withInput();
			$Fields = ['code','name','description','customer_side_view','similiar_to_status','after','agent_status','customer_status'];
			$Extra = ['created_by'	=>	$request->user()->partner];
			$DBFields = array_merge($request->only($Fields),$Extra);
      return ($Data->create($DBFields)) ? redirect()->route("ts.index") : redirect()->back()->withInput()->with(["info"=>true,"type"=>"danger","text"=>"Error in creating new ticket status."]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\App\Models\TicketStatus  $ticketStatus
     * @return \Illuminate\Http\Response
     */
    public function show($ticketStatus)
    {
        $Data = TicketStatus::find($ticketStatus);
				return view('ts.show',compact("Data"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\App\Models\TicketStatus  $ticketStatus
     * @return \Illuminate\Http\Response
     */
    public function edit($ticketStatus)
    {
        $Data = TicketStatus::find($ticketStatus);
				$TS = TicketStatus::whereStatus("ACTIVE")->pluck('name','code')->toArray();
				$Update = true;
				$PS = [''=>'(initial status)'] + $TS;
				$SS = [''=>'(none)'] + $TS;
				$MS = ['NEW','INPROCESS','COMPLETED'];
				return view("ts.form",compact("Data","Update","PS","MS",'SS'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\App\Models\TicketStatus  $ticketStatus
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $ticketStatus)
    {
			$Fields = ['code','name','description','customer_side_view','after','similiar_to_status','agent_status','customer_status'];
			$Req = $request->only($Fields);
			$TS = TicketStatus::find($ticketStatus);
			$Rules = $TS->ValidationRules(); $Msgs = $TS->ValidationMessages();
			foreach($Fields as $Field)
				if($Req[$Field] == $TS->$Field)
					unset($Rules[$Field],$Req[$Field]);
			$Validator = Validator::make($Req,$Rules,$Msgs);
			if($Validator->fails()) return redirect()->back()->withErrors($Validator)->withInput();
			$TS->update($Req);
			return redirect()->route('ts.edit',['ticketStatus'	=>	$request->code])->withInput()->with(["info"=>true,"type"=>"success","text"=>"Updated Successfully."]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\App\Models\TicketStatus  $ticketStatus
     * @return \Illuminate\Http\Response
     */
    public function destroy(TicketStatus $ticketStatus)
    {
        //
    }
    public function delete($ticketStatus)
    {
        $TS = TicketStatus::find($ticketStatus);
				$TS->update(["status"	=> "INACTIVE"]);
				return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Status of " . $TS->name . ", changed to INACTIVE."]);
    }
    public function undelete($ticketStatus)
    {
        $TS = TicketStatus::find($ticketStatus);
				$TS->update(["status"	=> "ACTIVE"]);
				return redirect()->back()->with(["info"=>true,"type"=>"success","text"=>"Status of " . $TS->name . ", changed to ACTIVE."]);
    }
}
