<div class="table-responsive">
	<table class="table table-bordered">
		<thead><tr>
			<th>No</th>
			<th>Message</th>
			<th>Response</th>
		</tr></thead><tbody>
			@forelse($sreqs as $k => $sreq)
			<tr>
				<td>{{ $loop->iteration }}</td>
				<td><small>{!! nl2br($sreq->message) !!}</small></td>
				<td>@if($sreq->response) nl2br($sreq->response) @endif</td>
			</tr>
			@empty
			<tr><th colspan="3" class="text-center">no records found!</th></tr>
			@endforelse
		</tbody>
	</table>
</div>
