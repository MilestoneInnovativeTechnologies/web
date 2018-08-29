@extends("company.page")
@section("content")

<div class="content distributor_panel">

	<a href="{{ url()->previous() }}" class="btn btn-default"><span class="glyphicon glyphicon-arrow-left"></span> Back</a><br><br>
	
	<div class="row">
		<div class="col col-md-8">
			<div class="panel panel-default">
				<div class="panel-heading clearfix"><strong>Transactions and Outstandings</strong><span class="pull-right" style="margin-right: 22px"><select onChange="LoadTransactions()" name="TransactionRecords" class="form-control">@foreach([10,20,30,50,80,100,150,200,250,300,500] as $n)<option value="{{ $n-1 }}">{{ $n }}</option>@endforeach</select></span></div>
				<div class="panel-body">
					<table class="table table-bordered transactions">
						<thead><tr><th>No</th><th>Date</th><th>Description</th><th>Amount</th><th title="To company">Outstandings</th></tr></thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="col col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>{{ $Partner["name"] }}</strong></div>
				<div class="panel-body">
					<table class="table" border="0" cellpadding="5" cellspacing="5">
						<tr><th width="40%" style="border: none">Code</th><th width="5%" style="border: none">:</th><td style="border: none">{{ $Partner["code"] }}</td></tr>
						<tr><th>Distributor Since</th><th>:</th><td>{{ date("d/M/Y",strtotime($Partner["created_at"])) }}</td></tr>@php $D = $Partner['details']; @endphp
						<tr><th>Address</th><th>:</th><td>{{ $D['address1'].", ".$D['address2'] }}<br>{{ $D["city"]['name'] }}, {{ $D["city"]['state']['name'] }}<br>{{ $D["city"]['state']['country']['name'] }}</td></tr>
						<tr><th>Current Status</th><th>:</th><td>{{ $Partner["status"] }}</td></tr>
						<tr><th>Status Remark</th><th>:</th><td>{{ $Partner["status_description"] }}</td></tr>
						<tr><th>Phone</th><th>:</th><td>+{{ $D["phonecode"] }}-{{ $D["phone"] }}</td></tr>
						<tr><th>Email</th><th>:</th><td>{{ implode(", ",array_column($Partner["logins"],"email")) }}</td></tr>
						<tr><th>Website</th><th>:</th><td>{{ $D['website'] }}</td></tr>

					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="panel panel-default customers">
		<div class="panel-heading"><strong>Customers</strong><div class="pull-right"><select onChange="RCDS(this.value)">@foreach([30,50,75,100,150,250,500] as $C) <option value='{{$C}}'>{{$C}}</option> @endforeach</select></div></div>
		<div class="panel-body">
			<table class="table table-striped table-bordered">
				<thead><tr><th>No</th><th>Customer</th><th>Dealer</th><th>Product</th><th>Edition</th><th>Added on</th><th>Registered on</th></tr></thead>
				<tbody></tbody>
			</table>
		</div>
	</div>

	<div class="panel panel-default dealers">
		<div class="panel-heading"><strong>Dealers</strong></div>
		<div class="panel-body">
			<table class="table table-striped table-bordered">
				<thead><tr><th rowspan="2">No</th><th rowspan="2">Dealer</th><th colspan="3" class="text-center">Customers</th></tr><tr><th class="text-center">Registered</th><th class="text-center">Unregistered</th><th class="text-center">Total</th></tr></thead>
				<tbody></tbody>
			</table>
		</div>
	</div>

	<div class="panel panel-default products">
		<div class="panel-heading"><strong>Products</strong></div>
		<div class="panel-body">
			<table class="table table-striped table-bordered">
				<thead><tr><th rowspan="2">No</th><th rowspan="2">Product</th><th rowspan="2">Edition</th><th colspan="3" class="text-center">Customers</th></tr><tr><th class="text-center">Registered</th><th class="text-center">Unregistered</th><th class="text-center">Total</th></tr></thead>
				<tbody>@foreach($Partner["products"] as $PC => $PO)
					<tr>
						<td rowspan="{{ count($PO[1])?:1 }}">{{ $loop->iteration }}</td>
						<td rowspan="{{ count($PO[1])?:1 }}">{{ $PO[0] }}</td>
						@foreach($PO[1] as $EC => $DS)
							@if($loop->iteration > 1) </tr><tr> @endif
							<td>{{ $DS }}</td>
							<td data-product="{{ $PC }}" data-edition="{{ $EC }}" class="reg text-center">0</td>
							<td data-product="{{ $PC }}" data-edition="{{ $EC }}" class="unreg text-center">0</td>
							<td data-product="{{ $PC }}" data-edition="{{ $EC }}" class="total text-center">0</td>
						@endforeach
					</tr>
				@endforeach</tbody>
			</table>
		</div>
	</div>

</div>

@endsection
@push("js")
<script type="text/javascript">
	var $_PartnerCode = '{{ $Partner['code'] }}', $_PartnerData, $_DDData, $_Transaction = false;
	var _ItemsNo = {{ app('request')->input('_')?:$Items }}, _DaysNo = {{ app('request')->input('__')?:0 }};
</script>
@endpush