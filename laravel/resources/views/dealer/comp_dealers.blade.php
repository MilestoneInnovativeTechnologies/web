<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Name</th><th>Since</th><tbody>
	@forelse($Dealers as $d)
	<tr><th>{{ $loop->iteration }}</th><td><a href="{{ Route(((session()->get('_company'))?'mit.':'').'dealer.panel',$d->code) }}" style="text-decoration: none; color: inherit">{{ $d->name }}</a></td><td>{{ date('d/M/Y',strtotime($d->created_at)) }}</td></tr>
	@empty
	<tr><th colspan="3" style="text-align: center">No records found!</th></tr>
	@endforelse
</tbody></table></div>