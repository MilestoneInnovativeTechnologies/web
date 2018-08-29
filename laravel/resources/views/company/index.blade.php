@extends("company.page")
@section("content")

<div class="content company_dashboard">
	<div class="clearfix">
		<div class="col-xs-6" style="padding-left: 0px">
			<div class="panel panel-default country_partners">
				<div class="panel-heading"><strong>Country wise Partners count</strong><div class="pull-right"><select onChange="Distribute_CWP(this.value)">@foreach([5,10,15,25] as $C) <option value='{{$C}}'>{{$C}}</option> @endforeach</select></div></div>
				<div class="panel-body">
					<table class="table table-bordered">
						<thead><tr><th>No</th><th>Country</th><th class="text-center">Distributors</th><th class="text-center">Dealers</th><th class="text-center">Customers</th><th class="text-center">Total</th></tr></thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
		
		<div class="col-xs-3" style="padding: 0px">
			<div class="panel panel-default industry_customers">
				<div class="panel-heading"><strong>Industry wise Customers</strong><div class="pull-right"><select onChange="Distribute_IWC(this.value)">@foreach([5,10,15,25] as $C) <option value='{{$C}}'>{{$C}}</option> @endforeach</select></div></div>
				<div class="panel-body">
					<table class="table table-bordered">
						<thead><tr><th>No</th><th>Industry</th><th class="text-center">Customers</th></tr></thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="col-xs-3" style="padding-right: 0px">
			<div class="prv" style="border-bottom: 1px solid #DDD">
				<div class="panel-body">
					<big class="page_record_view text-info text-center"></big>
					<div style="padding: 0px 25px;"><br><button class="btn btn-info form-control" onClick="EditPRV()">Change</button><a class="btn btn-link center-block text-center" href="javascript:location.search = '';">Reset</a></div>
					<div class="clearfix" style="display: none"><br>
						<div class="col-xs-5" style="padding: 0px"><select class="form-control" style="padding: 0px 1px" name="prv_req" onChange="ChangePRV()"><option value="__">Last</option><option value="_">Recent</option></select></div>
						<div class="col-xs-4" style="padding: 0px 3px"><input type="text" class="form-control" name="prv_cnt"></div>
						<div class="col-xs-3" style="padding: 7px 0px 0px"><label class="prv_lbl">Records</label></div>
						<a class="btn btn-link" href="javascript:CancelPRV()">Cancel</a><a class="btn btn-link" href="javascript:SubmitPRV()">Submit</a>
					</div>
				</div>
			</div>
			<div class="quick" style="padding-top: 10px; display: block">
				<div class="panel-body">
					<strong>Partners Panel</strong><br><br>
					<div class="form-group clearfix">
						<div class="col-xs-8" style="padding-left: 0px"><select name="VP_distributor" class="form-control" onChange="GetList('dealer',this.value)"><option>Select Distributor</option></select></div>
						<div class="pull-right col-xs-4"><button class="btn btn-default form-control" onClick="PN('S')">View</button></div>
					</div>
					<div class="form-group clearfix">
						<div class="col-xs-8" style="padding-left: 0px"><select name="VP_dealer" class="form-control" onChange="GetList('customer',this.value)"><option>Select Dealer</option></select></div>
						<div class="pull-right col-xs-4"><button class="btn btn-default form-control" onClick="PN('D')">View</button></div>
					</div>
					<div class="form-group clearfix">
						<div class="col-xs-8" style="padding-left: 0px"><select name="VP_customer" class="form-control"><option>Select Customer</option></select></div>
						<div class="pull-right col-xs-4"><button class="btn btn-default form-control" onClick="PN('C')">View</button></div>
					</div>
					
				</div>
			</div>
		</div>
	</div>

	<div class="panel panel-default company_products">
		<div class="panel-heading"><strong>Products</strong></div>
		<div class="panel-body">
			<table class="table table-responsive table-bordered products">
				<thead><tr><th rowspan="2" style="vertical-align: middle">No</th><th rowspan="2" style="vertical-align: middle">Product</th><th rowspan="2" style="vertical-align: middle">Edition</th><th colspan="2" class="text-center">Customers</th><th colspan="2" class="text-center">Total</th></tr><tr><th class="text-center">Registered</th><th class="text-center">Non Registered</th><th class="text-center">Edition</th><th class="text-center">Product</th></tr></thead>
				<tbody></tbody>
			</table>
		</div>
	</div>

	<div class="panel panel-default company_rac">
		<div class="panel-heading"><strong>Recently Added Customers</strong><div class="pull-right"><select onChange="DistributeTableData('rac',$_ACPE,this.value)">@foreach([5,10,15,25,40,50,75,100,150,200] as $C) <option value='{{$C}}'>{{$C}}</option> @endforeach</select></div></div>
		<div class="panel-body">
			<table class="table table-striped">
				<thead><tr><th>No</th><th>Customer</th><th>Distributor/Dealer</th><th>Product</th><th>Edition</th><th>Added On</th><th>Registered</th></tr></thead>
				<tbody></tbody>
			</table>
		</div>
	</div>

	<div class="panel panel-default company_rrc">
		<div class="panel-heading"><strong>Recently Registered Customers</strong><div class="pull-right"><select onChange="DistributeTableData('rrc',$_RCPE,this.value)">@foreach([5,10,15,25,40,50,75,100,150,200] as $C) <option value='{{$C}}'>{{$C}}</option> @endforeach</select></div></div>
		<div class="panel-body">
			<table class="table table-striped">
				<thead><tr><th>No</th><th>Customer</th><th>Distributor/Dealer</th><th>Product</th><th>Edition</th><th>Added On</th><th>Registered On</th></tr></thead>
				<tbody></tbody>
			</table>
		</div>
	</div>

</div>

@endsection
@push("js")
<script type="text/javascript">
	var _ItemsNo = {{ app('request')->input('_')?:$Items }}, _DaysNo = {{ app('request')->input('__')?:0 }};
	var _Customers = {!! $Partners->map(function($item,$key){ return ($item->role == "customer" && $item->parent_role != "customer" && ($item->parent_role == "dealer" || $item->parent_role == "distributor")) ? array_values($item->toArray()) : NULL; })->filter()->values() !!};
	var	_Dealers = {!! $Partners->map(function($item,$key){ return ($item->role == "dealer" && $item->parent_role == "distributor") ? array_values($item->toArray()) : NULL; })->filter()->values() !!};
	var _Distributors = {!! $Partners->map(function($item,$key){ return ($item->role == "distributor" && strtolower($item->parent_role) == "company") ? array_values($item->toArray()) : NULL; })->filter()->values() !!};
	var _Companies = {!! $Partners->map(function($item,$key){ return ($item->role == "company" || strtolower($item->role) == "company") ? array_values($item->toArray()) : NULL; })->filter()->values() !!};
	var _PE = {!! $PE !!};
	var _RouteLinks = {"customer":"{{Route('mit.customer.panel',['code'=>'--CODE--'])}}","dealer":"{{Route('mit.dealer.panel',['code'=>'--CODE--'])}}","distributor":"{{Route('mit.distributor.panel',['code'=>'--CODE--'])}}"}
</script>
@endpush