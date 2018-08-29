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
<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Task</th><th>Responder</th><th>Status</th></tr></thead><tbody>
	@forelse($Tasks as $t)
	<tr>
		<th>{{ $loop->iteration }}</th>
		<td>
			{{ $t->seqno }}. <a href="{{ Route('task.panel',$t->id) }}" style="color: inherit"><strong>{{ $t->title }}</strong></a><br>
			<small>{{ $t->description }}</small><hr style="margin: 5px 0px">
			<small><strong>Ticket:</strong> <a href="{{ Route('ticket.panel',$t->ticket) }}" style="color: inherit">{{ $t->Ticket->title }}</a></small>
		</td>
		<td>
			@if($t->Responder) <strong>{{ $t->Responder->Responder->name }}</strong><br>
			<small><strong>Assigned By:</strong> {{ $t->Responder->Assigner->name }}</small><br>
			<small><strong>On:</strong> {{ date('d/M h:i a',strtotime($t->Responder->created_at)) }}</small> @endif
		</td>
		<td>
			<strong>{{ $t->Cstatus->status }}</strong><br>
			<small>{{ SecDiff(time()-($t->Cstatus->start_time)) }}</small>
			@if($t->Cstatus->status == 'HOLD' && $t->Cstatus->status_text)<br><small>({{ $t->Cstatus->status_text }})</small> @endif
		</td>
	@empty
	<tr><th colspan="4" style="text-align: center">No records found!</th></tr>
	@endforelse
</tbody></table></div>
