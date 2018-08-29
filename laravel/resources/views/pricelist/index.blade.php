@extends("pricelist.page")
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Price Lists</strong><a href="{{ Route('pricelist.create')}}" class="btn btn-info pull-right btn-sm"><span class="glyphicon glyphicon-plus"></span> Create New Price List</a></div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-bordered">
					<thead><tr><th>No</th><th>Code</th><th>Name</th><th>Description</th><th>Actions</th></tr></thead>
					<tbody>
						@foreach(App\Models\PriceList::whereStatus("ACTIVE")->latest()->take(25)->get() as $no => $PL)
						<tr><td>{{ $no+1 }}</td><td>{{ $PL->code }}</td><td>{{ $PL->name }}</td><td>{{ $PL->description }}</td><td>
							<a href="{{ Route('pricelist.show',['pricelist'=>$PL->code]) }}" title="View details of {{ $PL->name }}" class="btn"><span class="glyphicon glyphicon-list-alt"></span></a>
							<a href="{{ Route('pricelist.edit',['pricelist'=>$PL->code]) }}" title="Edit details of {{ $PL->name }}" class="btn"><span class="glyphicon glyphicon-edit"></span></a>
							<form style="display:inline" method="post" action="{{ Route('pricelist.destroy',['pricelist'=>$PL->code]) }}">{{ method_field('DELETE') }}{{ csrf_field() }}<button type="submit" class="btn btn-none" style="background: none; border: none; color: #337ab7"><span title="Delete {{ $PL->name }}" class="glyphicon glyphicon-remove"></span></button></form>
						</td></tr>
						@endforeach
					</tbody>
				</table>
			</div>
			<div class="jumbotron" style="display: none">
				<h1 class="text-center">No Records found</h1>
				<p class="text-center"><a href="{{ Route('pricelist.create') }}" class="btn btn-primary text-center">Create New</a></p>
			</div>
		</div>
	</div>
</div>


@endsection