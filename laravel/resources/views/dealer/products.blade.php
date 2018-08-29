@extends("dealer.page")
@section("content")


<div class="content dealer_products">
	<form action="{{ Route('dealer.products',['dealer'	=>	$Code]) }}" method="post">
		{{ csrf_field() }}
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Dealer Products</strong><a href="{{ Route('dealer.index') }}" class="btn btn-default pull-right btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Back</a></div>
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
					<input type="submit" name="submit" value="Submit" class="btn btn-info">
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