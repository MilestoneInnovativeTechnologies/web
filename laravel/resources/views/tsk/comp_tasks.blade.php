@php
if(!function_exists('SecDiff')){
	function SecDiff($s){
		if(60 > $s) return $s . ' secs'; $d = 60;
		if(3600 > $s) { $min = floor($s/$d); $sec = $s%$d; return join(" ",[$min,'mins',$sec,'secs']); } $d = 3600;
		if(86400 > $s) { $hrs = floor($s/$d); $min = floor(($s%$d)/60); return join(" ",[$hrs,'hrs',$min,'mins']); } $d = 86400;
		if(2592000 > $s) { $dys = floor($s/$d); $hrs = floor(($s%$d)/3600); return join(" ",[$dys,'days',$hrs,'hrs']); }
		$d = 2592000;	$mn = floor($s/$d); $dys = floor(($s%$d)/86400); return join(" ",[$mn,'months',$dys,'days']);
	}
}
@endphp
<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Title &amp; Code</th><th>Customer &amp; Ticket</th><th>Created</th><th>Assigned</th><th>Status</th></tr></thead><tbody>
	@forelse($Tasks as $t)
	<tr>
		<th>{{ $loop->iteration }}</th>
		<td><a href="{{ Route('task.panel',$t->id) }}" style="color: inherit">{{ $t->title }}</a></td>
		<td><a href="{{ Route('customer.panel',$t->Ticket->customer) }}" style="color: inherit">{{ $t->Ticket->Customer->name }}</a><br><small>Ticket: <a href="{{ Route('ticket.panel',$t->ticket) }}" style="color: inherit">{{ $t->Ticket->title }}</a></small></td>
		<td>By: {{ $t->CreatedBy->name }}<br>On: <small>{{ date('d/M h:i a',strtotime($t->created_at)) }}</small></td>
		<td>By: {{ $t->Responder->Assigner->name }}<br>On: <small>{{ date('d/M h:i a',strtotime($t->Responder->created_at)) }}</small></td>
		<td>{{ $t->Cstatus->status }}<br><small>({{ SecDiff(time()-$t->Cstatus->start_time) }})</small>@if($t->Cstatus->status == 'HOLD')<br><small>(<strong>Hold Reason: </strong>{!! nl2br($t->Cstatus->status_text) !!})</small> @endif</td></tr>
	@empty
	<tr><th colspan="6" style="text-align: center">No records found!</th></tr>
	@endforelse
</tbody></table></div>
