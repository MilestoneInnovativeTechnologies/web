@php
if(!function_exists('SecondDiff')){
	function SecondDiff($s){
		if(60 > $s) return $s . ' secs'; $d = 60;
		if(3600 > $s) { $min = floor($s/$d); $sec = $s%$d; return join(" ",[$min,'mins',$sec,'secs']); } $d = 3600;
		if(86400 > $s) { $hrs = floor($s/$d); $min = floor(($s%$d)/60); return join(" ",[$hrs,'hrs',$min,'mins']); } $d = 86400;
		if(2592000 > $s) { $dys = floor($s/$d); $hrs = floor(($s%$d)/3600); return join(" ",[$dys,'days',$hrs,'hrs']); }
		$d = 2592000;	$mn = floor($s/$d); $dys = floor(($s%$d)/86400); return join(" ",[$mn,'months',$dys,'days']);
	}
}
@endphp
<div class="table-responsive"><table class="table table-striped">
	<thead><tr><th>No</th><th>Customer</th><th>Distributor/Dealer</th><th>Added On</th></tr></thead><tbody>
	@forelse($Data as $Reg)
		<tr>
			<td>{{ $loop->iteration }}</td>
			<td><a href="{{ Route('customer.panel',$Reg->customer) }}" style="color: inherit" target="_blank">{{ $Reg->Customer->name }}</a><br><small>{{ $Reg->Product->name }} {{ $Reg->Edition->name }} Edition</small></td>
			<td>@if($Reg->Customer->get_dealer() && $dealer = $Reg->Customer->get_dealer()) Dealer: <small><a href="{{ Route('dealer.panel',$dealer->code) }}" style="color: inherit" target="_blank">{{ $dealer->name }}</a></small><br> @endif Distributor: <small><a href="{{ Route('distributor.panel',$Reg->Customer->get_distributor()->code) }}" style="color: inherit" target="_blank">{{ $Reg->Customer->get_distributor()->name }}</a></small></td>
			<td>{{ date('D d/m/y',strtotime($Reg->created_at)) }}<br><small>({{ SecondDiff(time()-strtotime($Reg->created_at)) }})</small><br><small>Expire on: {{ date('D d/m/y',strtotime('+30 days',strtotime($Reg->created_at))) }}</small></td>
		</tr>
	@empty
		<tr><th colspan="4" class="text-center">No records found!</th></tr>
	@endforelse
	</tbody>
</table></div>