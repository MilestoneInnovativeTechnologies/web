@extends("tst.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Select Distributor</strong>{!! PanelHeadBackButton(Route('tst.customers',['code'=>$Code])) !!}</div>
		<div class="panel-body">
			<div class="form-group form-horizontal clearfix">
				<label class="control-label col-xs-2 text-left">Distributor</label>
				<div class="col-xs-5">
					<select name="distributor" class="form-control">@foreach($Distributors as $DCode => $DName)<option value="{{ $DCode }}">{{ $DName }}</option>@endforeach</select>
				</div>
				<div class="col-xs-5">
					<a class="btn btn-info" href="javascript:FetchDistributorCustomer()">Fetch Customers</a> | <a href="javascript:UpdateDistributors();" class="btn btn-primary disabled asc">Assign Selected Customers</a>
				</div>
			</div>
		</div>
	</div>
	<div class="panel panel-default customers" style="display: none">
		<div class="panel-heading"><strong>Customers</strong> &nbsp; <a href="javascript:SelectUnassigned('customers')" class="btn-link btn-sm">select unassigned customers</a> &nbsp; <a href="javascript:InvertSelection('customers')" class="btn-link btn-sm">invert selection</a></div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-bordered customers">
					<thead><tr><th>No</th><th>Name</th><th>Support Team</th><th>Product</th><th>Edition</th><th>Address</th><th>Contact</th></tr></thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="jumbotron" style="display: none">
		<h2 class="text-center">No Records found</h2>
	</div>
</div>

@endsection
@push('js')
<script type="text/javascript" src="js/tst_customers_assign.js"></script>
<script type="text/javascript">
	$_CODE="{{ $Code }}";
@if(Request()->_D != "")
	$(function(){
		$('[name="distributor"]').val('{{ Request()->_D }}');
		FetchDistributorCustomer();
	})
@endif
</script>

@endpush