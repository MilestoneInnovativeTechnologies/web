<div class="table-responsive"><table class="table table-striped">
	<thead><tr><th>No</th><th>Customer</th><th>Expire On</th></tr></thead><tbody>
	@forelse($Data as $Reg)
		<tr>
			<td>{{ $loop->iteration }}</td>
			<td><strong><a href="{{ Route('customer.panel',$Reg->customer) }}" style="color: inherit" target="_blank">{{ $Reg->Customer->name }}</a></strong><br><small>Product: {{ $Reg->Product->name }} {{ $Reg->Edition->name }} Edition</small><br>
			@if($Reg->Customer->get_dealer() && $dealer = $Reg->Customer->get_dealer())<small> Dealer: <a href="{{ Route('dealer.panel',$Reg->Customer->get_dealer()->code) }}" style="color: inherit" target="_blank">{{ $dealer->name }}</a></small><br>
			@endif <small>Distributor: <a href="{{ Route('distributor.panel',$Reg->Customer->get_distributor()->code) }}" style="color: inherit" target="_blank">{{ $Reg->Customer->get_distributor()->name }}</a></small></td>
			<td>{{ date('D d/m/y',strtotime('+30 days',strtotime($Reg->created_at))) }}<br><small>Added on: {{ date('D d/m/y',strtotime($Reg->created_at)) }}</small></td>
		</tr>
	@empty
		<tr><th colspan="4" class="text-center">No records found!</th></tr>
	@endforelse
	</tbody>
</table></div>