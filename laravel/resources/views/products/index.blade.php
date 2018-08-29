@extends("products.page")
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Products</strong><a href="products/create" class="btn btn-info pull-right btn-sm"><span class="glyphicon glyphicon-plus"></span> Create New Product</a></div>
		<div class="panel-body">
			@if(isset($products) && !empty($products))
			<div class="table-responsive">
				<table class="table table-bordered">
					<thead>
						<tr><th rowspan="2" style="text-align: center; vertical-align: middle" width="5%">No</th><th rowspan="2" style="vertical-align: middle" width="10%">Code</th><th rowspan="2" style="vertical-align: middle" width="10%">Display Name</th><th rowspan="2" style="vertical-align: middle" width="10%">Base Name</th><th colspan="2">Description</th><th colspan="6" rowspan="2" style="text-align: center; vertical-align: middle" width="15%">Actions</th></tr>
						<tr><th width="25%">Internal</th><th>Public</th></tr>
					</thead>
					<tbody>
					@foreach($products->all() as $Iter => $ProductArray)
						<tr>
							<td>{{ $Iter+1 }}</td>
							<td>{{ $ProductArray['code'] }}</td>
							<td>{{ $ProductArray['name'] }}{!! ($ProductArray['private'] == "YES")?' (<small>Private</small>)':'' !!}</td>
							<td>{{ $ProductArray['basename'] }}</td>
							<td>{{ $ProductArray['description_internal'] }}</td>
							<td>{{ $ProductArray['description_public'] }}</td>
							<td class="text-center">
								<a href="product/{{ $ProductArray['code'] }}/editions" class="btn" data-toggle="tooltip" title="View/Edit Editions of {{ $ProductArray['name'] }}"><span class="glyphicon glyphicon-book"></span></a>
								<a href="product/{{ $ProductArray['code'] }}/features" class="btn" data-toggle="tooltip" title="View/Edit Features of {{ $ProductArray['name'] }}"><span class="glyphicon glyphicon-tags"></span></a>
								<a href="product/{{ $ProductArray['code'] }}/packages" class="btn" data-toggle="tooltip" title="View/Edit Packages of {{ $ProductArray['name'] }}"><span class="glyphicon glyphicon-hdd"></span></a>
								<a href="product/{{ $ProductArray['code'] }}" class="btn" data-toggle="tooltip" title="View details of {{ $ProductArray['name'] }}"><span class="glyphicon glyphicon-list-alt"></span></a>
								<a href="products/{{ $ProductArray['code'] }}" class="btn" data-toggle="tooltip" title="Edit details of {{ $ProductArray['name'] }}"><span class="glyphicon glyphicon-edit"></span></a>
								<a href="product/delete/{{ $ProductArray['code'] }}" class="btn" data-toggle="tooltip" title="Delete {{ $ProductArray['name'] }}"><span class="glyphicon glyphicon-remove"></span></a>
							</td>
						</tr>
					@endforeach
					</tbody>
					<tfoot>
						<tr><td colspan="5">&nbsp;</td>
							<td class="options" colspan="2"><form method="get" action="products">
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