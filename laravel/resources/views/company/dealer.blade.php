@extends("company.page")
@section("content")

<div class="content dealer_panel">

	<a href="{{ Route('dashboard') }}" class="btn btn-default"><span class="glyphicon glyphicon-arrow-left"></span> Back</a><br><br>
	
	<div class="row">
		<div class="col col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>{{ $Partner["name"] }}</strong></div>
				<div class="panel-body">
					<table class="table" border="0" cellpadding="5" cellspacing="5">
						<!--<tr><th colspan="3" style="border: none">&nbsp;</td></tr>-->
						<tr><th width="25%" style="border: none">Code</th><th width="5%" style="border: none">:</th><td style="border: none">{{ $Partner["code"] }}</td></tr>
						<tr><th>Dealer Since</th><th>:</th><td>{{ date("d/M/Y",strtotime($Partner["created_at"])) }}</td></tr>@php $D = $Partner['details']; @endphp
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
		<div class="col col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Distributor Details</strong></div>@php $P = $Partner['parent_details'][0]; $PD = $P['details']; @endphp
				<div class="panel-body">
					<table class="table" border="0" cellpadding="5" cellspacing="5">
						<tr><th width="25%" style="border: none">Name</th><th width="5%" style="border: none">:</th><td style="border: none">{{ $P["name"] }}</td></tr>
						<tr><th>Address</th><th>:</th><td>{{ $PD['address1'].", ".$PD['address2'] }}<br>{{ $PD["city"]['name'] }}, {{ $PD["city"]['state']['name'] }}<br>{{ $PD["city"]['state']['country']['name'] }}</td></tr>
						<tr><th>Current Status</th><th>:</th><td>{{ $P["status"] }}</td></tr>
						<tr><th>Status Remark</th><th>:</th><td>{{ $P["status_description"] }}</td></tr>
						<tr><th>Phone</th><th>:</th><td>+{{ $PD["phonecode"] }}-{{ $PD["phone"] }}</td></tr>
						<tr><th>Email</th><th>:</th><td>{{ implode(", ",array_column($P["logins"],"email")) }}</td></tr>
						<tr><th>Website</th><th>:</th><td>{{ $PD['website'] }}</td></tr>
					</table>
				</div>
			</div>
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

	<div class="panel panel-default customers">
		<div class="panel-heading"><strong>Customers</strong></div>
		<div class="panel-body">
			<table class="table table-striped table-bordered">
				<thead><tr><th>No</th><th>Customer</th><th>Product</th><th>Edition</th><th>Added on</th><th>Registered on</th></tr></thead>
				<tbody></tbody>
			</table>
		</div>
	</div>

</div>

@endsection
@push("js")
<script type="text/javascript">
	var $_PartnerCode = '{{ $Partner['code'] }}', $_PartnerData;
	var _ItemsNo = {{ app('request')->input('_')?:$Items }}, _DaysNo = {{ app('request')->input('__')?:0 }};
	var isDealer = true;
</script>
@endpush