<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Name</th><th>Since</th></tr></thead><tbody>
	@forelse($Agents as $a)
	<tr><th>{{ $loop->iteration }}</th><td><a href="{{ Route((session()->get('_company')?'mit.':'').'supportagent.panel',$a->code) }}" style="color: inherit">{{ $a->name }}</a></td><td>{{ date('d/M/Y',strtotime($a->created_at)) }}</td></tr>
	@empty
	<tr><th colspan="4" style="text-align: center">No records found!</th></tr>
	@endforelse
</tbody></table></div>