@extends("tsk.page")
@section("content")
@php
$Task = \App\Models\TicketTask::whereId(Request()->code)->with(['Stype','Responder.Assigner','Status','Ticket' => function($Q){ $Q->with('Tasks','Conversations'); }])->first();
//dd($Task->toArray());
@endphp

<div class="content">
	<div class="row">
		<div class="col col-md-12">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">{{ $Task->title }}</div></div><div class="panel-body">
				<div class="table table-responsive"><table class="table striped"><tbody>
					<tr><th>Task Sequence</th><th>:</th><td><strong>{{ $Task->seqno }}</strong></td><th>Support Type</th><th>:</th><td>{{ ($Task->support_type)?$Task->Stype->name:'' }}</td></tr>
					<tr><th>Title</th><th>:</th><td>{{ $Task->title }}</td><th>Weightage</th><th>:</th><td>{{ ($Task->weightage)?:'0' }} / 100</td></tr>
					<tr><th>Description</th><th>:</th><td>{!! nl2br($Task->description) !!}</td><th>Handle</th><th>:</th><td>@if($Task->handle_after) <strong>After</strong>@foreach($Task->handle_after as $ha)<br><strong>{{ $ha->seqno }}. </strong>{{ $ha->title }}@endforeach @else IMMEDIATE @endif</td></tr>
					<tr><th>Responder</th><th>:</th><td>@if($Task->Responder) <strong>{{ $Task->Responder->Responder->name }}</strong> @endif</td><th>Assigner</th><th>:</th><td>@if($Task->Responder) {{ $Task->Responder->Assigner->name }} @endif</td></tr>
					<tr><th>Created On</th><th>:</th><td>{{ date('D d/M/y h:i a',strtotime($Task->created_at)) }}</td><th>Assigned On</th><th>:</th><td>@if($Task->Responder) {{ date('D d/M/y h:i a',strtotime($Task->Responder->created_at)) }} @endif</td></tr>
					<tr><th>Current Status</th><th>:</th><td><strong>{{ $Task->CStatus->status }}</strong><br><small>({{ SecDiff(time()-($Task->CStatus->start_time)) }})</small></td><th>Status Info</th><th>:</th><td>{!! nl2br($Task->Cstatus->status_text) !!}</td></tr>
				</tbody></table></div>
			</div></div>
		</div>
	</div>
	<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Other Tasks</div></div><div class="panel-body">
	@component('tsk.comp_othertasks',['Tasks' => $Task->Ticket->Tasks, 'Skip' => $Task->id]) @endcomponent
	</div></div>
	<div class="row">
		<div class="col col-md-6">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Task Status Flow</div></div><div class="panel-body"><div class="table-responsive"><table class="table striped"><tbody>
			@forelse(GetTaskTimings($Task->Status) as $Field => $Value)
			<tr><th>{{ $Field }}</th><th>:</th><td>{{ $Value }}</td></tr>
			@empty
			<tr><th style="text-align: center">No Details available</th></tr>
			@endforelse
			</tbody></table></div></div></div>
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Chat Transcript</div></div><div class="panel-body" style="max-height: 400px; overflow-y: scroll">
			@component('tkt.comp_conversations',['Conversations' => $Task->Ticket->Conversations]) @endcomponent
			</div></div>
		</div>
		<div class="col col-md-6" style="padding-left: 0px;"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Task Status Flow</div></div><div class="panel-body">
		@component('tsk.comp_statusflow',['Status' => $Task->Status]) @endcomponent
		</div></div></div>
	</div>
</div>

@endsection
@php
function GetTaskTimings($Status){
	if($Status->isEmpty()) return null;
	$Start = 0; $TimeToWork = 0; $Worked = 0; $Holded = 0; $StartToClose = 0;
	foreach($Status as $State){
		if($Start == 0 && $State->status == 'CREATED'){ $Start = $State->start_time; continue; }
		if($Start && $State->status == 'WORKING'){
			if($TimeToWork == 0) $TimeToWork = $State->start_time - $Start;
			$Worked += $State->total;
			continue;
		}
		if($TimeToWork && $State->status == 'HOLD') { $Holded += $State->total; continue; }
		if($Start && $TimeToWork && $Worked && $State->status == 'CLOSED') { $StartToClose = $State->start_time - $Start; continue; }
	}
	return ['Time to Work' => SecDiff($TimeToWork), 'Total Worked' => SecDiff($Worked), 'Total Holded' => SecDiff($Holded), 'Total Time' => SecDiff($StartToClose)];
}
function SecDiff($s){
	if(60 > $s) return $s . ' secs'; $d = 60;
	if(3600 > $s) { $min = floor($s/$d); $sec = $s%$d; return join(" ",[$min,'mins',$sec,'secs']); } $d = 3600;
	if(86400 > $s) { $hrs = floor($s/$d); $min = floor(($s%$d)/60); return join(" ",[$hrs,'hrs',$min,'mins']); } $d = 86400;
	if(2592000 > $s) { $dys = floor($s/$d); $hrs = floor(($s%$d)/3600); return join(" ",[$dys,'days',$hrs,'hrs']); }
	$d = 2592000;	$mn = floor($s/$d); $dys = floor(($s%$d)/86400); return join(" ",[$mn,'months',$dys,'days']);
}
@endphp