@extends("customer.page")
@section("content")

<div class="content customer_register">
	
	<form action="{{ Route('customer.register',['customer'=>$Data['code'],'seqno'=>$Seqno]) }}" method="post" enctype="multipart/form-data">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Product Registration</strong><a href="{{ Route('customer.index') }}" class="btn btn-default btn-sm pull-right"><span class="glyphicon glyphicon-arrow-left"></span> &nbsp; Back</a></div>
		<div class="panel-body">
			<div class="clearfix">
				<div class="col-xs-5">
					<table class="table table-striped">
						<tr><th>Product</th><th>:</th><th>{{ $Data['register'][0]['product']['name'] }}</th></tr>
						<tr><th>Edition</th><th>:</th><th>{{ $Data['register'][0]['edition']['name'] }}</th></tr>
						<tr><th>Licence File</th><th>:</th><th><input type="file" class="form-control" name="licence" onChange="LicChanged()" required></th></tr>
					</table>
				</div> <div class="col-xs-7">&nbsp;</div>
			</div>
			<hr>
			<table class="table">
				<thead><tr><th>Fields</th><th>Data from Database</th><th>Data from Licence file</th><th>Okey to proceed</th></tr></thead>
				<tbody>
					<tr class="CompanyName"><th>Customer/Company</th><td class="db">{{ $Data['name'] }}</td><td class="lf"></td><td class="text-center ok"></td></tr>
					<tr class="SoftwareName"><th>Software Name</th><td class="db">{{ $Data['register'][0]['product']['name'] }} {{ $Data['register'][0]['edition']['name'] }} Edition</td><td class="lf"></td><td class="text-center ok"></td></tr>
					<!--<tr class="Address1"><th>Address Line 1</th><td class="db">{{ $Data['details']['address1'] }}</td><td class="lf"></td><td class="text-center ok"></td></tr>
					<tr class="Address2"><th>Address Line 2</th><td class="db">{{ $Data['details']['address2'] }}</td><td class="lf"></td><td class="text-center ok"></td></tr>
					<tr class="City"><th>City</th><td class="db">{{ $Data['details']['city']['name'] }}</td><td class="lf"></td><td class="text-center ok"></td></tr>
					<tr class="State"><th>State</th><td class="db">{{ $Data['details']['city']['state']['name'] }}</td><td class="lf"></td><td class="text-center ok"></td></tr>-->
					<tr class="Country"><th>Country</th><td class="db" data-sortname="{{ $Data['details']['city']['state']['country']['sortname'] }}">{{ $Data['details']['city']['state']['country']['name'] }}</td><td class="lf"></td><td class="text-center ok"></td></tr>
					<tr class="eMail"><th>Email</th><td class="db">{{ $Data['logins'][0]['email'] }}</td><td class="lf"></td><td class="text-center ok"></td></tr>
				</tbody>
			</table>
		</div>
		<div class="panel-footer clearfix">
			<div class="pull-right">
				<input type="submit" name="action" value="Request Registeration Key" class="btn btn-info" disabled>
			</div>
		</div>
	</div></form>

	
</div>

@endsection
@push("js")
<script type="text/javascript" src="js/customer_licence.js"></script>
@endpush