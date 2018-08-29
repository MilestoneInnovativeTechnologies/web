<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>App Name</th><th>Login</th><th>Secret</th><th>Remarks</th></tr></thead><tbody>
	@forelse($Connections as $Cn)
	<tr><th>{{ $loop->iteration }}</th><th>{{ $Cn->appname }}</th><td>{{ $Cn->login }}</td><td>{{ $Cn->secret }}</td><td>{!! nl2br($Cn->remarks) !!}</td></tr>
	@empty
	<tr><th colspan="5" style="text-align: center">No records found!</th></tr>
	@endforelse
</tbody></table></div>