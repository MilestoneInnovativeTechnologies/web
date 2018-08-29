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
<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Ticket Details</th><th>Status</th><th>Responders</th></tr></thead><tbody>
	@forelse($Tickets as $t)
	<tr>
		<th>{{ $loop->iteration }}</th>
		<td>
			<a href="{{ Route('ticket.panel',$t->code) }}" style="color: inherit"><small>{{ $t->code }}</small></a><br>
			<strong>{{ $t->title }}</strong><br>
			<small>{{ $t->description }}</small><hr style="margin: 4px 0px">
			<small><strong>Customer:</strong>  <a href="{{ Route('customer.panel',$t->customer) }}" style="color: inherit">{{ $t->Customer->name }}</a></small><br>
			<small><strong>Product:</strong> {{ $t->Product->name }} {{ $t->Edition->name }} Edition</small><hr style="margin: 4px 0px">
			<strong>Category: </strong> @if($t->category){{ $t->Category->name }}@endif @if($t->Category_specs && $t->Category_specs->isNotEmpty()) @foreach($t->Category_specs as $spec)<br><small><strong>{{ $spec->Specification->name }}: </strong> {{ ($spec->value_text)?:$spec->Value->name }}</small>@endforeach @endif
		</td>
		<td>
			<strong>{{ $t->Cstatus->status }}</strong><br>
			<small>{{ $t->Cstatus->User->name }}</small><br>
			<small>{{ SecDiff(time()-($t->Cstatus->start_time)) }}</small>
			@if($t->Cstatus->status == 'HOLD' && $t->Cstatus->status_text)<br> <small>({!! nl2br($t->Cstatus->status_text) !!})</small>@endif
		</td>
		<td style="font-size: 12px">{!! implode("<br>",$t->Tasks->map(function($item){ return '<b>'.$item->seqno.'. </b>' . (($item->Responder)?$item->Responder->Responder->name:''); })->toArray()) !!}</td>
	</tr>
	@empty
	<tr><th colspan="5" style="text-align: center">No records found!</th></tr>
	@endforelse
</tbody></table></div>
