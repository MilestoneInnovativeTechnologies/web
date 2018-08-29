<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Name</th><th>Since</th></tr></thead><tbody>
	@forelse($Distributors as $d)
	<tr><th>{{ $loop->iteration }}</th><td><a href="{{ Route('distributor.panel',$d->Partner->code) }}" style="color: inherit">{{ $d->Partner->name }}</a></td><td>{{ date('d/M/Y',strtotime($d->created_at)) }}</td></tr>
	@empty
	<tr><th colspan="4" style="text-align: center">No records found!</th></tr>
	@endforelse
</tbody></table></div>