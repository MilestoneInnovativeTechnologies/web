<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Name</th><th>Value</th><th>Created By</th></tr></thead><tbody>
	@forelse($Cookies as $Ck)
	<tr><th>{{ $loop->iteration }}</th><th>{{ $Ck->name }}</th><td>{!! nl2br($Ck->value) !!}</td><td>{{ $Ck->CreatedBy->name }}</td></tr>
	@empty
	<tr><th colspan="4" style="text-align: center">No records found!</th></tr>
	@endforelse
</tbody></table></div>