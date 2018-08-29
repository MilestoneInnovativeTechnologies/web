@extends("features.page")
@section("content")

<div class="panel panel-default">
	<div class="panel-heading"><strong>{{ $code['name'] }}</strong><a href="features" class="btn btn-default pull-right btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Back</a></div>
	<div class="panel-body">
		<div class="table-responsive">
			<table class="table table-striped">
				<tbody>
					<tr><td width="15%"><strong>ID</strong></td><td>{{ $code['id'] }}</td></tr>
					<tr><td><strong>Category</strong></td><td>{{ ($code['category'])?$code['category']:'Others' }}</td></tr>
					<tr><td><strong>Value type</strong></td><td>{{ $code['value_type'] }}</td></tr>
					@if(($code['value_type'] == "OPTION" || $code['value_type'] == "MULTISELECT") && !empty($code["options"]))
					<tr><td><strong>Available Options</strong></td><td>
						<ol>
						@foreach($code["options"] as $OptArray)
							<li>{{ $OptArray['option'] }}</li>
						@endforeach
						</ol>
					</td></tr>
					@endif
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