@extends("tsk.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Task Details</strong>{!! PanelHeadBackButton((url()->previous() == url()->current())?(Route('tsk.index')):(url()->previous())) !!}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col col-md-6">
					<div class="table table-responsive">
						<table class="table table-striped">
							<tbody>
								<tr><th>Task ID: {{ $tsk->id }}</th><th>Task Seqno: {{ $tsk->seqno }}</th></tr>
								<tr><th>Title</th><td>{{ $tsk->title }}</td></tr>
								<tr><th>Description</th><td>{{ $tsk->description }}</td></tr>
								<tr><th>Support Type</th><td>{{ ($tsk->support_type)?$tsk->Stype->name:'' }}</td></tr>
								<tr><th>Assigned To</th><td>{{ ($tsk->Responder)?$tsk->Responder->Responder->name:'None' }} &nbsp; &nbsp; <small>(Weightage: {{ $tsk->weightage }})</small></td></tr>
								<tr><th>Assigned By</th><td>@if($tsk->Responder) {{ $tsk->Responder->Assigner->name }} &nbsp; &nbsp; on <script>document.write(ReadableDate('{{ $tsk->Responder->created_at }}'))</script> @endif</td></tr>
								<tr><th>Active</th><td>{{ $tsk->status }}@unless($tsk->status == 'ACTIVE') &nbsp; &nbsp; <small>(Waiting for some tasks to be closed)</small> @endunless</td></tr>
								<tr><th>Task Status</th><td>@if($tsk->Cstatus) <strong>{{ $tsk->Cstatus->status }}</strong> &nbsp; &nbsp; {{ SecDiff(time()-strtotime($tsk->Cstatus->created_at)) }} ago @endif</td></tr>
								<tr><th>Status Text</th><td>{{ $tsk->Cstatus->status_text }}</td></tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="col col-md-6">
					<div class="table table-responsive">
						<table class="table table-striped">
							<tbody>
								<tr><th colspan="2">Ticket Details</th></tr>@php $tkt = $tsk->Ticket @endphp
								<tr><th>Ticket Code</th><td>{{ $tkt->code }}</td></tr>
								<tr><th>Customer</th><td>{{ $tkt->Customer->name }}</td></tr>
								<tr><th>Title</th><td>{{ $tkt->title }}</td></tr>
								<tr><th>Description</th><td>{{ $tkt->description }}</td></tr>
								<tr><th>Created On</th><td><script>document.write(ReadableDate('{{ $tkt->created_at }}')); document.write(' &nbsp; &nbsp; '); d = DateDiff('{{ $tkt->created_at }}','d'); h = DateDiff('{{ $tkt->created_at }}','h'); document.write(d+" days, "+parseInt(d/h)+" hours ago")</script></td></tr>
								<tr><th>Current Status</th><td><strong>{{ $tkt->Cstatus->status }}</strong> &nbsp; &nbsp; {{ SecDiff(time()-strtotime($tsk->Cstatus->created_at)) }} ago</td></tr>
								<tr><th>Status Text</th><td>{{ $tkt->Cstatus->status_text }}</td></tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>@if($tsk->Cstatus && $tsk->Cstatus->status == 'CLOSED' && $tsk->load('Status') && $Tims = GetTaskTimings($tsk->Status))
			<div class="row">
				<div class="col col-md-4"><div class="table-responsive"><table class="table table-stripped"><tbody>
					<tr><th>Time to Start Work</th><td>{{ $Tims[0] }}</td></tr>
					<tr><th>Total Woked</th><td>{{ $Tims[1] }}</td></tr>
					<tr><th>Total Holded</th><td>{{ $Tims[2] }}</td></tr>
					<tr><th>Total Time</th><td>{{ $Tims[3] }}</td></tr>
				</tbody></table></div></div>
				<div class="col col-md-9"><div class="table-responsive"><table class="table table-stripped"><tbody>
					
				</tbody></table></div></div></div>
			</div>@endif
		</div>
	</div>
</div>

@endsection
@php
function GetTaskTimings($Status){
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
	return [SecDiff($TimeToWork),SecDiff($Worked),SecDiff($Holded),SecDiff($StartToClose)];
}
function SecDiff($s){
	if(60 > $s) return $s . ' secs'; $d = 60;
	if(3600 > $s) { $min = floor($s/$d); $sec = $s%$d; return join(" ",[$min,'mins',$sec,'secs']); } $d = 3600;
	if(86400 > $s) { $hrs = floor($s/$d); $min = floor(($s%$d)/60); return join(" ",[$hrs,'hrs',$min,'mins']); } $d = 86400;
	if(2592000 > $s) { $dys = floor($s/$d); $hrs = floor(($s%$d)/3600); return join(" ",[$dys,'days',$hrs,'hrs']); }
	$d = 2592000;	$mn = floor($s/$d); $dys = floor(($s%$d)/86400); return join(" ",[$mn,'months',$dys,'days']);
}
@endphp