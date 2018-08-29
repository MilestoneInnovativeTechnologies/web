@extends("editions.page")
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Editions</strong><a href="editions/create" class="btn btn-info pull-right btn-sm"><span class="glyphicon glyphicon-plus"></span> Create New Edition</a></div>
		<div class="panel-body">
			@if(isset($editions) && !empty($editions))
			<div class="table-responsive">
				<table class="table table-bordered">
					<thead>
						<tr><th rowspan="2" style="text-align: center; vertical-align: middle" width="5%">No</th><th rowspan="2" style="vertical-align: middle" width="10%">Code</th><th rowspan="2" style="vertical-align: middle" width="10%">Name</th><th colspan="2">Description</th><th rowspan="2" style="text-align: center; vertical-align: middle" width="15%">Actions</th></tr>
						<tr><th width="25%">Internal</th><th>Public</th></tr>
					</thead>
					<tbody>
					@foreach($editions->all() as $Iter => $EditionArray)
						<tr>
							<td>{{ $Iter+1 }}</td>
							<td>{{ $EditionArray['code'] }}</td>
							<td>{{ $EditionArray['name'] }}{!! ($EditionArray['private'] == "YES")?' (<small>Private</small>)':'' !!}</td>
							<td>{{ $EditionArray['description_internal'] }}</td>
							<td>{{ $EditionArray['description_public'] }}</td>
							<td class="text-center">
								<a href="editions/{{ $EditionArray['code'] }}" class="btn" data-toggle="tooltip" title="View details of {{ $EditionArray['name'] }}"><span class="glyphicon glyphicon-list-alt"></span></a>
								<a href="editions/{{ $EditionArray['code'] }}/edit" class="btn" data-toggle="tooltip" title="Edit details of {{ $EditionArray['name'] }}"><span class="glyphicon glyphicon-edit"></span></a>
								<form method="post" action="editions/{{ $EditionArray['code'] }}" style="display: inline">{{ csrf_field() }}<input type="hidden" name="_method" value="delete"><button type="submit" class="btn btn-none" style="background: none; color: #337ab7"><span class="glyphicon glyphicon-remove"></span></button></form>
							</td>
						</tr>
					@endforeach
					</tbody>
					<tfoot>
						<tr><td colspan="4">&nbsp;</td>
							<td class="options" colspan="2"><form method="get" action="editions">
								Records per page: <select name="items_per_page">
									<option value="10"<?=($ItemsPerPage == 10)?" selected":""?>>10</option>
									<option value="25"<?=($ItemsPerPage == 25)?" selected":""?>>25</option>
									<option value="50"<?=($ItemsPerPage == 50)?" selected":""?>>50</option>
									<option value="75"<?=($ItemsPerPage == 75)?" selected":""?>>75</option>
									<option value="100"<?=($ItemsPerPage == 100)?" selected":""?>>100</option>
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