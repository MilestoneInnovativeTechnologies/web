@extends("distributor.page")
@section("content")


<div class="content distributor_show">

	<div class="panel panel-default main">
		<div class="panel-heading"><strong></strong><a href="{{ Route('distributor.index') }}" class="btn btn-default pull-right btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> &nbsp; Back</a></div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-striped">
					<tbody>
						<tr><td width="15%"><strong>Address</strong></td><td><p class="address"></p></td></tr>
						<tr><td><strong>Contacts</strong></td><td><p class="contacts"></p></td></tr>
						<tr><td><strong>Countries</strong></td><td><p class="countries"></p></td></tr>
						<tr><td><strong>Currency</strong></td><td><p class="currency"></p></td></tr>
						<tr><td><strong>Distributor Since</strong></td><td><p class="since"></p></td></tr>
						<tr><td><strong>Price List Name</strong></td><td><p class="pricelist"></p></td></tr>
						<tr><td><strong>Status</strong></td><td><p class="status"></p></td></tr>
						<tr><td><strong>Products</strong></td><td><ol class="products"></ol></td></tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection
@push("js")
<script type="text/javascript">
var _Distributor = '{{ $Code }}';
</script>
@endpush