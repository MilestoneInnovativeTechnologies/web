@extends("products.page")
@section("content")

<div class="panel panel-default">
	<div class="panel-heading"><strong>{{ $code['name'] }}</strong>{!! ($code['private'] == 'YES')?' (<small>Private</small>)':'' !!}<a href="{{ url()->previous() }}" class="btn btn-default pull-right btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Back</a></div>
	<div class="panel-body">
		<div class="table-responsive">
			<table class="table table-striped">
				<tbody>
					<tr><td width="15%"><strong>Code</strong></td><td>{{ $code['code'] }}</td></tr>
					<tr><td><strong>Private</strong></td><td>{{ $code['private'] }}</td></tr>
					<tr><td><strong>Description <small>Public</small></strong></td><td>{{ $code['description_public'] }}</td></tr>
					<tr><td><strong>Description <small>Internal</small></strong></td><td>{{ $code['description_internal'] }}</td></tr>
					<tr><td><strong>Base Name</strong></td><td>{{ $code['basename'] }}</td></tr>
					<tr><td><strong>Last updated on</strong></td><td>{{ date('D d/M/Y', strtotime($code['updated_at'])) }}</td></tr>
					<tr><td><strong>Created on</strong></td><td>{{ date('D d/M/Y', strtotime($code['created_at'])) }}</td></tr>
					<tr><td colspan="2"></td></tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

@if($code['editions'])
<div class="panel panel-default">
  <div class="panel-heading"><strong>Editions</strong></div>
  <div class="panel-body">
		<div class="table-responsive">
			<table class="table table-striped">
				<thead><tr><th width="5%">#</th><th width="40%">Name</th><th width="50%">Description</th><th width="5%">Level</th></tr></thead>
				<tbody>
					@foreach($code['editions'] as $Iter => $EditionsArray)
					<tr>
						<th>{{ $Iter+1 }}</th>
						<td><strong>{{ $EditionsArray->name }}</strong><br><br>{{ $EditionsArray->description_public }}</td>
						<td>{{ $EditionsArray->pivot->description }}</td>
						<th>{{ $EditionsArray->pivot->level }}</th>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
@endif

@if($code['features'])
<div class="panel panel-default">
  <div class="panel-heading"><strong>Features</strong></div>
  <div class="panel-body">
		<div class="table-responsive">
			<table class="table table-striped">
				<thead><tr><th width="5%">#</th><th width="65%">Details</th><th width="25%">Values</th><th width="5%">Order</th></tr></thead>
				<tbody>
					@foreach($code['features'] as $Iter => $FeatureArray)
					<tr>
						<th>{{ $Iter+1 }}</th>
						<td><strong>{{ $FeatureArray->name }}</strong><br><br>{{ $FeatureArray->description_public }}</td>
						<td>{{ $FeatureArray->pivot->value }}</td>
						<td><strong>{{ $FeatureArray->pivot->order }}</strong></td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
@endif

@endsection