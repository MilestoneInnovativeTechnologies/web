@extends("tsk.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Ticket Tasks</strong></div>
		<div class="panel-body">
			<div class="clearfix pagination">
				<div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search for tickets" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchText()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
				<div class="pull-right">{{ $Links }}</div>
			</div>@if($Data->isNotEmpty())
			<div class="table-responsive">
				<table class="table table-bordered">
					<thead><tr><th>Ticket/Customer</th><th>Task Title</th><th>Support Type</th><th>Responder</th><th>Created On</th><th>Closed On</th><th>Time On Task</th><th>Action</th></tr></thead>
					<tbody>@foreach($Data as $tkt => $Array)
						<tr>
							<td rowspan="{{ $Array->count() }}" style="vertical-align: middle"><strong>{{ $tkt }}</strong><br>{{ $Array[0]->Ticket->Customer->name }}</td>
							@foreach($Array as $Obj)
							<td><strong>{{ $Obj->seqno }}.</strong> {{ $Obj->title }}</td>
							<td>{{ ($Obj->support_type)?$Obj->Stype->name:'' }}</td>
							<td>{{ $Obj->Responder->Responder->name }}</td>
							<td>{!! date('d/M/Y\<\b\r\>h:i A',strtotime($Obj->created_at)) !!}</td>
							<td>{!! date('d/M/Y\<\b\r\>h:i A',$Obj->Cstatus->start_time) !!}</td>
							<td nowrap>{!! GetTaskTimings($Obj->Status) !!}</td>
							<td>{!! GetActions($Obj) !!}</td>
							@if($loop->remaining) </tr><tr> @endif
							@endforeach
						</tr>
					@endforeach</tbody>
				</table>
			</div>@else
			<div class="jumbotron">
				<h2 class="text-center">No Records found</h2>
			</div>@endif
		</div>
	</div>
</div>

@endsection
@php
function GetActions($tsk){
	$Actions = $tsk->available_action;
	$TitleIcon = ['view'=>['View task details','list-alt'],'edit'=>['Edit task details','edit'],'delete'=>['Delete this task','remove'],'chngrsp'=>['Assign/Change responder','user'],'open'=>['Open this task','open-file'],'recheck'=>['Submit for rechecking the task','refresh'],'work'=>['Start/Continue handling this task','forward'],'hold'=>['Hold this task','pause'],'close'=>['Close this task','ok']];
	$Id = $tsk->id;
	return implode("",array_map(function($action)use($Id,$TitleIcon){
		return glyLink(Route('tsk.'.$action,['tsk'=>$Id]), $TitleIcon[$action][0], $TitleIcon[$action][1], ['class'=>'btn btn-none']);
	},$Actions));
}
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
	return implode('<br>',['Time to Work: '.SecDiff($TimeToWork),'Total Worked: '.SecDiff($Worked),'Total Holded: '.SecDiff($Holded),'Total Time: '.SecDiff($StartToClose)]);
}
function SecDiff($s){
	if(60 > $s) return $s . ' secs'; $d = 60;
	if(3600 > $s) { $min = floor($s/$d); $sec = $s%$d; return join(" ",[$min,'mins',$sec,'secs']); } $d = 3600;
	if(86400 > $s) { $hrs = floor($s/$d); $min = floor(($s%$d)/60); return join(" ",[$hrs,'hrs',$min,'mins']); } $d = 86400;
	if(2592000 > $s) { $dys = floor($s/$d); $hrs = floor(($s%$d)/3600); return join(" ",[$dys,'days',$hrs,'hrs']); }
	$d = 2592000;	$mn = floor($s/$d); $dys = floor(($s%$d)/86400); return join(" ",[$mn,'months',$dys,'days']);
}
@endphp