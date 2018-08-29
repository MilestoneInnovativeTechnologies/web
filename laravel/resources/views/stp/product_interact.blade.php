@extends("stp.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Products</strong></div>
		<div class="panel-body">
			<div class="table table-responsive">
				<table class="table table-striped product">
					<thead><tr><th>Product</th><th>Editions</th><th>Latest Version</th><th>Action</th></tr></thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection
@push('js')
<script type="text/javascript">
	_Product = {!! json_encode($Product) !!};
</script>
<script type="text/javascript" src="js/stp_product_interact.js"></script>
@endpush