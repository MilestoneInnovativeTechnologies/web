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
$Category = $Tickets->groupBy(function($item){ return ($item->category) ? $item->Category->name : 'Other'; });
@endphp
<div class="table-responsive">
	@forelse($Category as $Name => $Tickets)
	@if($Tickets->isNotEmpty())
	<table class="table table-bordered"><caption>{{ $Name }}</caption><thead><tr><th>No</th><th>Ticket</th><th>Responders</th></tr></thead><tbody>
		@foreach($Tickets as $t)
		<tr>
			<th>{{ $loop->iteration }}</th>
			<td>
				<a href="{{ Route('ticket.panel',$t->code) }}" style="color: inherit">{{ $t->title }}</a><br>
				<small><strong>Customer:</strong> <a href="{{ Route('customer.panel',$t->customer) }}" style="color: inherit">{{ $t->Customer->name }}</a></small><br>
				<small><strong>Status:</strong> {{ $t->Cstatus->status }}</small>
			</td>
			<td>@php $rs = $t->get_responders(); @endphp @if($rs && $rs->isNotEmpty()) <ol style="padding-left: 10px"><li><small>{!! $rs->implode('</small></li><li><small>') !!}</small></li></ol> @endif</td>
{{--			<td>{{ $t->code }}</td>--}}
		</tr>
		@endforeach
	</tbody></table>
	@endif
	@empty
	<tr><th colspan="3" style="text-align: center">No records found!</th></tr>
	@endforelse
</div>