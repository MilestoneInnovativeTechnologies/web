@extends("pricelist.page")
@section("content")

<div class="content">

	<div class="panel panel-default">
		<div class="panel-heading"><strong>{{ $Details["name"] }}</strong><a href="{{ url()->previous() }}" class="btn btn-default pull-right btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Back</a></div>
		<div class="panel-body">
			<p>{{ $Details["description"] }}</p><hr>
			<table class="table table-striped">
				<thead><tr><th>No</th><th>Product</th><th>Edition</th><th>MOP</th><th>Price</th><th>MRP</th><th>Currency</th></tr></thead>
				<tbody>
					@foreach($Details["items"] as $k => $item)
						<tr>
							<td>{{ $k+1 }}</td>
							@foreach(["product","edition","mop","price","mrp","currency"] as $key)
								<td>{{ $item[$key] }}</td>
							@endforeach
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>

@endsection