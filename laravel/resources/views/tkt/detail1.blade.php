@extends("tkt.page")
@section("content")
@php
$Ticket = \App\Models\Ticket::whereCode(Request()->code)->with(['Product','Edition','CreatedBy','Customer','Team','conversations','Tasks' => function($Q){ $Q->with('Stype','CreatedBy','Responder.Assigner'); },'Closure','Feedback','Attachments'])->first();
//dd($Ticket->toArray());
@endphp

<div class="content">
	<div class="row">
		<div class="col col-md-12">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">{{ $Ticket->title }}</div></div><div class="panel-body">
				<div class="table table-responsive"><table class="table striped"><tbody>
					<tr><th>Code</th><th>:</th><td>{{ $Ticket->code }}</td><th>Ticket Type</th><th>:</th><td>{{ ($Ticket->ticket_type)?$Ticket->Type->name:'' }}</td></tr>
					<tr><th>Title</th><th>:</th><td>{{ $Ticket->title }}</td><th>Created</th><th>:</th><td>By: {{ $Ticket->CreatedBy->name }}<br>On: {{ date('d/M/Y',strtotime($Ticket->created_at)) }}</td></tr>
					<tr><th>Description</th><th>:</th><td>{!! nl2br($Ticket->description) !!}</td><th>Category</th><th>:</th><td>{{ ($Ticket->category)?$Ticket->Category->name:'Other' }}@if($Ticket->Category_specs && $Ticket->Category_specs->isNotEmpty()) @foreach($Ticket->Category_specs as $spec) <br><small><strong>{{ $spec->Specification->name }}: </strong>{{ ($spec->value_text)?:$spec->Value->name }}</small> @endforeach @endif</td></tr>
					<tr><th>Customer</th><th>:</th><td><strong><a href="{{ Route('customer.panel',$Ticket->customer) }}" style="color: inherit">{{ $Ticket->Customer->name }}</a></strong></td><th>Product</th><th>:</th><td>{{ $Ticket->Product->name }} {{ $Ticket->Edition->name }} Edition</td></tr>
					<tr><th>Support Team</th><th>:</th><th><a href="{{ Route('supportteam.panel',$Ticket->Team->team) }}" style="color: inherit">{{ $Ticket->Team->Team->name }}</a></th><th>Current Status</th><th>:</th><td><strong>{{ $Ticket->Cstatus->status }}</strong> <small>({{ Sec2Ago(time()-$Ticket->Cstatus->start_time) }})</small>@if($Ticket->Cstatus->status == 'HOLD') <br><small>{!! nl2br($Ticket->Cstatus->status_text) !!}</small> @endif</td></tr>
					<tr><th>Priority</th><th>:</th><td><strong>{{ $Ticket->priority }}</strong></td><th>Attachments</th><th>:</th><td>@forelse($Ticket->Attachments as $ath) <a href="{{ Route('ticket.download.attachment',$ath->file) }}">@if($ath->name){{ $ath->name }}@else[NO NAME]@endif</a>@if($loop->remaining), @endif @empty - @endforelse</td></tr>
				</tbody></table></div>
			</div></div>
		</div>
	</div>
	<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Ticket Tasks</div></div><div class="panel-body">
	@component('tsk.comp_list_tkt',['Tasks' => $Ticket->Tasks]) @endcomponent
	</div></div>
	<div class="row">
		<div class="col col-md-4"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Ticket Timings</div></div><div class="panel-body">
			<div class="table-responsive"><table class="table table-striped"><tbody>
			@forelse(GetTicketTimings($Ticket->Status) as $Field => $Value)
			<tr><th>{{ $Field }}</th><th>:</th><td>{{ $Value }}</td></tr>
			@empty
			<tr><th style="text-align: center">No Details available</th></tr>
			@endforelse
			</tbody></table></div>
		</div></div></div>
		<div class="col col-md-4" style="padding-left: 0px"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Ticket Closure</div></div><div class="panel-body">
			<div class="table-responsive"><table class="table table-striped"><tbody>@if($Ticket->Closure)
			<tr><th>Solution Provided</th><th>:</th><td>{!! nl2br($Ticket->Closure->solution) !!}</td></tr>
			<tr><th>Referred Ticket</th><th>:</th><td>@if($Ticket->Closure->Reference) {{ $Ticket->Closure->Reference->title }}<br><small>{{ $Ticket->Closure->Reference->code }}</small> @endif</td></tr>
			<tr><th>Closure Document</th><th>:</th><td>@if($Ticket->Closure->support_doc) {{ json_decode($Ticket->Closure->support_doc,true)['name'] }}<br><small><a href="{{ Route('tkt.closuredoc',$Ticket->code) }}" target="_blank" download>download</a></small> @endif</td></tr>
			<tr><th>User</th><th>:</th><td>@if($Ticket->Closure->user) {{ $Ticket->Closure->User->name }} @endif</td></tr>
			@else <tr><th colspan="3" style="text-align: center">No Closure done!</th></tr>@endif
			</tbody></table></div>
		</div></div></div>
		<div class="col col-md-4" style="padding-left: 0px"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Ticket Feedback</div></div><div class="panel-body">
			<div class="table-responsive"><table class="table table-striped"><tbody>@if($Ticket->Feedback)
			<tr><th>Rating</th><th>:</th><td><strong>{{ $Ticket->Feedback->points }} / 10</strong></td></tr>
			<tr><th>FeedBack</th><th>:</th><td>@if($Ticket->Feedback->feedback) {!! nl2br($Ticket->Feedback->feedback) !!}@endif</td></tr>
			@else <tr><th colspan="3" style="text-align: center">No Feedback provided!</th></tr>@endif
			</tbody></table></div>
		</div></div></div>
	</div>
	<div class="row">
		<div class="col col-md-6"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Ticket Status Flow</div></div><div class="panel-body" style="max-height: 400px; overflow-y: scroll">
		@component('tkt.comp_statusflow',['Status' => $Ticket->Status]) @endcomponent
		</div></div></div>
		<div class="col col-md-6" style="padding-left: 0px;"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Chat Transcript</div></div><div class="panel-body" style="max-height: 400px; overflow-y: scroll">
		@component('tkt.comp_conversations',['Conversations' => $Ticket->Conversations]) @endcomponent
		</div></div></div>
	</div>
</div>

@endsection
@php
function GetCategoryBreadCrumb($Obj){
	$BCAry = [];
	if($Obj) $BCAry[] = $Obj->name;
	if($Obj->parent) array_unshift($BCAry,GetCategoryBreadCrumb($Obj->Parent));
	return implode(" &raquo; ", $BCAry);
}
function GetTicketTimings($Status){
	$Start = 0; $TimeToOpen = 0; $OpenToWork = 0; $Worked = 0; $Holded = 0; $StartToClose = 0;
	foreach($Status as $State){
		if($Start == 0 && $State->status == 'NEW'){ $Start = $State->start_time; continue; }
		if($Start && $State->status == 'OPENED'){ $TimeToOpen = $State->start_time - $Start; continue; }
		if($TimeToOpen && $State->status == 'INPROGRESS'){
			if($OpenToWork == 0) $OpenToWork = $State->start_time - $Start - $TimeToOpen;
			$Worked += $State->total;
			continue;
		}
		if($OpenToWork && $State->status == 'HOLD') { $Holded += $State->total; continue; }
		if($Start && $OpenToWork && $Worked && $State->status == 'CLOSED') { $StartToClose = $State->start_time - $Start; continue; }
	}
	return ['Delay to Open' => Sec2Ago($TimeToOpen),'Opened to Working' => Sec2Ago($OpenToWork),'Total Worked' => Sec2Ago($Worked),'Total Holded' => Sec2Ago($Holded),'Total Time' => Sec2Ago($StartToClose)];
}
function Sec2Ago($s){
	if(60 > $s) return $s . ' secs'; $d = 60;
	if(3600 > $s) { $min = floor($s/$d); $sec = $s%$d; return join(" ",[$min,'mins',$sec,'secs']); } $d = 3600;
	if(86400 > $s) { $hrs = floor($s/$d); $min = floor(($s%$d)/60); return join(" ",[$hrs,'hrs',$min,'mins']); } $d = 86400;
	if(2592000 > $s) { $dys = floor($s/$d); $hrs = floor(($s%$d)/3600); return join(" ",[$dys,'days',$hrs,'hrs']); }
	$d = 2592000;	$mn = floor($s/$d); $dys = floor(($s%$d)/86400); return join(" ",[$mn,'months',$dys,'days']);
}
@endphp