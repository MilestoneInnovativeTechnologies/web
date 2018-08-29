@extends("editions.page")
@section("content")

<div class="panel panel-default">
	<div class="panel-heading"><strong>{{ $code['name'] }}</strong><a href="{{ url()->previous() }}" class="btn btn-default pull-right btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Back</a></div>
	<div class="panel-body">
		<div class="table-responsive">
			<table class="table table-striped">
				<tbody>
					<tr><td width="15%"><strong>Code</strong></td><td>{{ $code['code'] }}</td></tr>
					<tr><td><strong>Private</strong></td><td>{{ $code['private'] }}</td></tr>
					<tr><td><strong>Description <small>Public</small></strong></td><td>{{ $code['description_public'] }}</td></tr>
					<tr><td><strong>Description <small>Internal</small></strong></td><td>{{ $code['description_internal'] }}</td></tr>
					<tr><td><strong>Last updated on</strong></td><td>{{ date('D d/M/Y', strtotime($code['updated_at'])) }}</td></tr>
					<tr><td><strong>Created on</strong></td><td>{{ date('D d/M/Y', strtotime($code['created_at'])) }}</td></tr>
				</tbody>
			</table>
		</div>
	</div>
</div>


@endsection