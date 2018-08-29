@extends("tsk.page")
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><div class="panel-title">Task Lists @if(isset($Title) && $Title) - <small>{{ $Title }}</small>@endif </div></div>
		<div class="panel-body">
			<div class="clearfix pagination">
				<div class="pull-right">{{ $Data->appends(['period' => Request()->period])->links() }}</div>
			</div>
			<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Task Details</th><th>Customer &amp; Ticket</th><th>Created</th><th>Assigned</th><th>Status</th></tr></thead><tbody>
			@forelse($Data as $tsk)
			<tr>
				<td>{{ $loop->iteration }}</td>
				<td><a href="{{ Route('task.panel',$tsk->id) }}" style="color: inherit">{{ $tsk->title }}</a><br><small>{!! nl2br($tsk->description) !!}</small></td>
				<td>{{ $tsk->Ticket->Customer->name }}<br><small>{{ $tsk->Ticket->title }}</small></td>
				<td>By: {{ $tsk->CreatedBy->name }}<br>On: {{ date('d/M/y',strtotime($tsk->created_at)) }}</td>
				<td>To: {{ $tsk->Responder->Responder->name }}<br>By: {{ $tsk->Responder->Assigner->name }}<br>On: {{ date('d/M/y',strtotime($tsk->Responder->created_at)) }}</td>
				<td><strong>{{ $tsk->Cstatus->status }}</strong>@if($tsk->Cstatus->status == 'HOLD' && $tsk->Cstatus->status_text)<br><small>({!! nl2br($tsk->Cstatus->status_text) !!})</small>@endif<br>{{ date('d/M h:i a',$tsk->Cstatus->start_time) }}<br><small>({{ Sec2Ago(time()-$tsk->Cstatus->start_time) }})</small></td>
			</tr>
			@empty
			<tr><th style="text-align: center" colspan="6">No records found!</th></tr>
			@endforelse
		</tbody></table></div>
		</div>
	</div>
</div>

@endsection
@php
function Sec2Ago($s){
	if(60 > $s) return $s . ' secs'; $d = 60;
	if(3600 > $s) { $min = floor($s/$d); $sec = $s%$d; return join(" ",[$min,'mins',$sec,'secs']); } $d = 3600;
	if(86400 > $s) { $hrs = floor($s/$d); $min = floor(($s%$d)/60); return join(" ",[$hrs,'hrs',$min,'mins']); } $d = 86400;
	if(2592000 > $s) { $dys = floor($s/$d); $hrs = floor(($s%$d)/3600); return join(" ",[$dys,'days',$hrs,'hrs']); }
	$d = 2592000;	$mn = floor($s/$d); $dys = floor(($s%$d)/86400); return join(" ",[$mn,'months',$dys,'days']);
}
@endphp