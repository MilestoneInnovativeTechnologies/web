@extends("features.page")
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Features</strong><a href="features/create" class="btn btn-info pull-right btn-sm"><span class="glyphicon glyphicon-plus"></span> Create New Feature</a></div>
		<div class="panel-body">
			@if(isset($features) && !empty($features))
			<div class="table-responsive">
				<table class="table table-bordered">
					<thead>
						<tr><th rowspan="2" width="5%">No</th><th rowspan="2" width="10%">Name</th><th rowspan="2" width="10%">Category</th><th rowspan="2" width="10%">Value Type</th><th colspan="2" width="50%">Description</th><th rowspan="2" width="15%">Actions</th></tr>
						<tr><th width="25%">Internal</th><th>Public</th></tr>
					</thead>
					<tbody>
					@foreach($features->all() as $Iter => $FeatureArray)
						<tr>
							<td>{{ $Iter+1 }}</td>
							<td>{{ $FeatureArray['name'] }}</td>
							<td>{{ $FeatureArray['category'] }}</td>
							<td>{{ $FeatureArray['value_type'] }}</td>
							<td>{{ $FeatureArray['description_internal'] }}</td>
							<td>{{ $FeatureArray['description_public'] }}</td>
							<td>
								<a href="features/{{ $FeatureArray['id'] }}" class="btn" data-toggle="tooltip" title="View details of {{ $FeatureArray['name'] }}"><span class="glyphicon glyphicon-list-alt"></span></a>
								<a href="features/{{ $FeatureArray['id'] }}/edit" class="btn" data-toggle="tooltip" title="Edit details of {{ $FeatureArray['name'] }}"><span class="glyphicon glyphicon-edit"></span></a>
								<form method="post" style="display: inline" action="features/{{ $FeatureArray['id'] }}">{{ csrf_field() }}<input type="hidden" name="_method" value="delete"><button type="submit" class="btn btn-none" style="background: none; color:#337AB7"><span class="glyphicon glyphicon-remove"></span></button></form></td>
						</tr>
					@endforeach
					</tbody>
					<tfoot>
						<tr><td colspan="5">&nbsp;</td>
							<td class="options" colspan="4"><form method="get" action="features">
							Records per page: <select name="items_per_page">
								<option value="50"<?=($ItemsPerPage == 50)?" selected":""?>>50</option>
								<option value="100"<?=($ItemsPerPage == 100)?" selected":""?>>100</option>
								<option value="150"<?=($ItemsPerPage == 150)?" selected":""?>>150</option>
								<option value="200"<?=($ItemsPerPage == 200)?" selected":""?>>200</option>
								<option value="250"<?=($ItemsPerPage == 250)?" selected":""?>>250</option>
							</select>
							Page No: <input type="text" name="page" value="{{ $PageNumber }}" size="1">
							<input type="submit" value="Go">
							</form></td>
						</tr>
					</tfoot>
				</table>
			</div>
			@else
			<div class="jumbotron">
				<h3 class="text-center">No Records found</h3>
			</div>
			@endif			
		</div>
	</div>
</div>


@endsection