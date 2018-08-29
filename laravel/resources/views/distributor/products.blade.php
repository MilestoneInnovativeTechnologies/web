@extends("distributor.page")
@section("content")


<div class="content distributor_products">

	<form action="{{ Route('distributor.products',['distributor'	=>	$Code]) }}" method="post">
		{{ csrf_field() }}
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Distributor Products</strong><a href="{{ Route('distributor.index') }}" class="btn btn-default pull-right btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> &nbsp; Back</a></div>
			<div class="panel-body">
				<div class="table table-responsive">
					<table class="table table-striped products">
						<thead><tr><th width="20%">Product</th><th width="20%">Edition</th><th width="10%">Currency</th><th width="20%">Price</th><th>Cost</th><th>MRP</th><th width="45">&nbsp;</th></tr></thead>
						<tbody></tbody>
						<tfoot><tr><td><a href="javascript:AddOneMoreLine()" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-plus"></span> &nbsp; New Line</a> </td><td colspan="6">&nbsp;</td></tr></tfoot>
					</table>
				</div>
			</div>
			<div class="panel-footer clearfix">
				<div class="pull-right">
					<input type="submit" name="submit" value="submit" class="btn btn-info">
				</div>
			</div>
		</div>
	</form>
</div>

@endsection
@push("js")
<script type="text/javascript">
var _Products = {!! json_encode($Products) !!}, _Editions = {!! json_encode($Editions) !!};
var PreDefinedValues = [@foreach($Current as $OBJ)['{{ $OBJ["product"] }}','{{ $OBJ["edition"] }}'],@endforeach];
var _PriceList = new Object({!! json_encode($PL) !!})
</script>
@endpush