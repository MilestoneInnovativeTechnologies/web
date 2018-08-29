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
<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Ticket Details</th><th>Customer</th><th>Status</th><th>Responders</th></tr></thead><tbody>
	@forelse($Tickets as $t)
	<tr>
		<th>{{ $loop->iteration }}</th>
		<td><a href="{{ Route('ticket.panel',$t->code) }}" style="color: inherit"><small>{{ $t->code }}</small></a><br><strong>{{ $t->title }}</strong><br><small>{{ $t->description }}</small></td>
		<td><a href="{{ Route('customer.panel',$t->customer) }}" style="color: inherit">{{ $t->Customer->name }}</a><br><small>{{ $t->Product->name }} {{ $t->Edition->name }} Edition</small></td>
		<td><strong>{{ $t->Cstatus->status }}</strong><br><small>{{ $t->Cstatus->User->name }}</small><br><small>{{ SecDiff(time()-($t->Cstatus->start_time)) }}</small></td>
		<td><small>{!! implode(", ",$t->Tasks->map(function($item){ return ($item->Responder)?$item->Responder->Responder->name:null; })->toArray()) !!}</small></td>
	</tr>
	@empty
	<tr><th colspan="5" style="text-align: center">No records found!</th></tr>
	@endforelse
</tbody></table></div>
