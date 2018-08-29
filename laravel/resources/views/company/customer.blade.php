@extends("company.page")
@section("content")

<div class="content customer_panel">

	<a href="{{ Route('dashboard') }}" class="btn btn-default"><span class="glyphicon glyphicon-arrow-left"></span> Back</a><br><br>
	
	<div class="row">
		<div class="col col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>{{ $Partner["name"] }}</strong></div>
				<div class="panel-body">
					<table class="table" border="0" cellpadding="5" cellspacing="5">
						<!--<tr><th colspan="3" style="border: none">&nbsp;</td></tr>-->
						<tr><th width="25%" style="border: none">Code</th><th width="5%" style="border: none">:</th><td style="border: none">{{ $Partner["code"] }}</td></tr>
						<tr><th>Customer Since</th><th>:</th><td>{{ date("d/M/Y",strtotime($Partner["created_at"])) }}</td></tr>@php $D = $Partner['details']; @endphp
						<tr><th>Address</th><th>:</th><td>{{ $D['address1'].", ".$D['address2'] }}<br>{{ $D["city"]['name'] }}, {{ $D["city"]['state']['name'] }}<br>{{ $D["city"]['state']['country']['name'] }}</td></tr>
						<tr><th>Industry</th><th>:</th><td>{{ $D['industry']['name'] }}</td></tr>
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
				<div class="panel-heading"><strong>Distributor/Dealer Details</strong></div>@php $P = $Partner['parent_details'][0]; $PD = $P['details']; @endphp
				<div class="panel-body">
					<table class="table" border="0" cellpadding="5" cellspacing="5">
						<?php $Roles = array_column($P['roles'],"displayname"); unset($Roles[array_search("Company",$Roles)]); unset($Roles[array_search("Customer",$Roles)]); ?>
						<tr><th colspan="3" style="border: none"><strong>{{ implode(", ",$Roles) }}</strong></td></tr>
						<tr><th width="25%">Name</th><th width="5%">:</th><td>{{ $P["name"] }}</td></tr>
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

	<div class="panel panel-default">
		<div class="panel-heading"><strong>Products</strong></div>
		<div class="panel-body">
			<table class="table table-striped table-bordered">
				<thead><tr><th>No</th><th>Product</th><th>Edition</th><th>Added on</th><th>Registered on</th><th>Version using</th><th>Last used on</th></tr></thead>
				<tbody>@foreach($Partner["products"] as $PC => $PO)
					<tr>
						<td rowspan="{{ count($PO[1])?:1 }}">{{ $loop->iteration }}</td>
						<td rowspan="{{ count($PO[1])?:1 }}">{{ $PO[0] }}</td>
						@foreach($PO[1] as $k => $DS)
							@if($loop->iteration > 1) </tr><tr> @endif
							<td>{{ $DS[0] }}</td>
							<td>{{ date("d/M/Y",strtotime($DS[1])) }}</td>
							<td>{{ $DS[2]? date("d/M/Y",strtotime($DS[2])) : "X" }}</td>
							<td></td>
							<td></td>
						@endforeach
					</tr>
				@endforeach</tbody>
			</table>
		</div>
	</div>

</div>

@endsection