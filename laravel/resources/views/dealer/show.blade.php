@extends("dealer.page")
@section("content")


<div class="content dealer_show">
	<div class="panel panel-default main">
		<div class="panel-heading"><strong></strong><a href="{{ Route('dealer.index') }}" class="btn btn-default pull-right btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Back</a></div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-striped table-condensed">
					<tbody>
						<tr><td width="15%"><strong>Address</strong></td><td><p class="address"></p></td></tr>
						<tr><td><strong>Phone</strong></td><td><p class="phone"></p></td></tr>
						<tr><td><strong>Email</strong></td><td><p class="email"></p></td></tr>
						<tr><td><strong>Countries</strong></td><td><p class="countries"></p></td></tr>
						<tr><td><strong>Currency</strong></td><td><p class="currency"></p></td></tr>
						<tr><td><strong>Dealer Since</strong></td><td><p class="since"></p></td></tr>
						<tr><td><strong>Status</strong></td><td><p class="status"></p></td></tr>
						<tr><td><strong>Products</strong></td><td><ul class="products"></ul></td></tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection
@push("js")
<script type="text/javascript">
var _Dealer = '{{ $Code }}';
</script>
@endpush