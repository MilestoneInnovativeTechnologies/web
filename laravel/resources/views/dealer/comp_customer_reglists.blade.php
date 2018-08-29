<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Customer</th><th>Product</th><th>Added On</th><th>Registered On</th><tbody>
	@forelse($Customers as $code => $regs)
	<tr><th rowspan="{{ count($regs) }}">{{ $loop->iteration }}</th><td rowspan="{{ count($regs) }}">{{ $regs[0][0] }}</td>
	@foreach($regs as $reg)
	<td>{{ $reg[1] }} {{ $reg[2] }} Edition</td><td>{{ date('d/M/Y',strtotime($reg[3])) }}</td><td>@if($reg[4]) {{ date('d/M/Y',strtotime($reg[4])) }} @else <span class="glyphicon glyphicon-remove"></span> @endif</td>
	@if($loop->remaining) </tr><tr> @endif
	@endforeach
	</tr>
	@empty
	<tr><th colspan="5" style="text-align: center">No records found!</th></tr>
	@endforelse
</tbody></table></div>