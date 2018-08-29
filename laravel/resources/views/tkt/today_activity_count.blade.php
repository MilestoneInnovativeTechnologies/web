<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Agent</th><th>Opened</th><th>Closed</th><th>Pending</th><th>Inprogress</th><th>Hold</th><th>Total</th></tr></thead><tbody>
	@forelse($Data as $t)
	<tr class="text-center"><th>{{ $loop->iteration }}</th><th>{{ $t->name }}</th><td>{{ $t->opened }}</td><td>{{ $t->closed }}</td><td>{{ $t->pending }}</td><td>{{ $t->inprogress }}</td><td>{{ $t->hold }}</td><td style="font-weight:bold">{{ $t->total }}</td></tr>
	@if($loop->last)
	<tr class="text-center" style="font-weight: bold"><td>&nbsp;</td><th>Total</th><td>{{ $Data->sum->opened }}</td><td>{{ $Data->sum->closed }}</td><td>{{ $Data->sum->pending }}</td><td>{{ $Data->sum->inprogress }}</td><td>{{ $Data->sum->hold }}</td><td style="font-weight: 900">{{ $Data->sum->total }}</td></tr>
	@endif
	@empty
	<tr><th colspan="8" style="text-align: center">No records found!</th></tr>
	@endforelse
</tbody></table></div>
