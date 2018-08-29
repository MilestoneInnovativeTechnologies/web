<div class="table-responsive"><table class="table table-striped">
	<thead><tr><th>No</th><th>Product</th><th>Status</th><th>Action</th></tr></thead><tbody>
	@forelse($Data as $Reg)
		<tr>
			<td>{{ $loop->iteration }}</td>
			<td>{{ $Reg->Product->name }} {{ $Reg->Edition->name }} Edition</td>
			<td>@if($Reg->requisition) Register Request Sent @else Unregistered @endif</td>
			<td><a class="btn btn-info" href="{{ Route('register.product',$Reg->seqno) }}"> @if($Reg->requisition) Send Reigister Request Again @else Register @endif </a></td>
		</tr>
	@empty
		<tr><th colspan="4" class="text-center">No records found!</th></tr>
	@endforelse
	</tbody>
</table></div>