@extends("customer.page")
@section("content")

<div class="content customer_show">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>{{ $Data->name }}</strong><a href="{{ Route('customer.index') }}" class="btn btn-default btn-sm pull-right"><span class="glyphicon glyphicon-arrow-left"></span> &nbsp; Back</a></div>
		<div class="panel-body">
			<div class="row">
				<div class="col col-md-6">
					<div class="col-xs-5 text-right"><strong>Code</strong></div><div class="col-xs-1">:</div><div class="col-xs-6">{{ $Data->code }}</div>
					<div class="col-xs-5 text-right"><strong>Customer Since</strong></div><div class="col-xs-1">:</div><div class="col-xs-6">{{ Date("d/M/Y",strtotime($Data->created_at)) }}</div>
					<div class="col-xs-5 text-right"><strong>Current Status</strong></div><div class="col-xs-1">:</div><div class="col-xs-6">{{ $Data->status }}</div>
					<div class="col-xs-5 text-right"><strong>Status Description</strong></div><div class="col-xs-1">:</div><div class="col-xs-6">{{ $Data->status_description }}&nbsp;</div>
					@if(session("_rolename") == "distributor")
					<div class="col-xs-5 text-right"><strong>Dealer</strong></div><div class="col-xs-1">:</div><div class="col-xs-6">{{ in_array("dealer",array_column($Data->parentDetails[0]->roles->toArray(),"name"))?$Data->parentDetails[0]->name:"-" }}</div>
					@endif
				</div>
				<div class="col col-md-6">
					<div class="clearfix"><div class="col-xs-5 text-right"><strong>Address</strong></div><div class="col-xs-1">:</div><div class="col-xs-6">@if($Data->details) {{ $Data->details->address1 }}, {{ $Data->details->address2 }}<br>@if($Data->details->city) {{ $Data->details->City->name }}, {{ $Data->details->City->State->name }}<br>{{ $Data->details->City->State->Country->name }} @endif @endif </div></div>
					<div class="clearfix"><div class="col-xs-5 text-right"><strong>Email</strong></div><div class="col-xs-1">:</div><div class="col-xs-6">{{ $Data->Logins->pluck("email")[0] }}</div></div>
					<div class="clearfix"><div class="col-xs-5 text-right"><strong>Phone</strong></div><div class="col-xs-1">:</div><div class="col-xs-6">@if($Data->details) +{{ $Data->details->phonecode }}-{{ $Data->details->phone }} @endif</div></div>
				</div>
			</div>
			<div class="clearfix">
				<div class="col-xs-12"><hr></div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">Products</div>
				<div class="panel-body">
					<table class="table table-responsive table-striped">
						<thead>
							<tr><th rowspan="2">No</th><th rowspan="2">Product</th><th rowspan="2">Edition</th><th rowspan="2">Added</th><th rowspan="2">Registered</th><th colspan="4">Presale</th></tr>
							<tr><th>End</th><th>Exended to</th><th>Exended by</th><th>Status</th></tr>
						</thead>
						<tbody>
							@foreach($Data->register as $RO)
							<tr>
								<td>{{ $loop->iteration }}</td>
								<td>{{ $RO->Product->name }}</td>
								<td>{{ $RO->Edition->name }}</td>
								<td>{{ date("d/M/Y",strtotime($RO->created_at)) }}</td>
								<td>{!! ($RO->registered_on) ? date("d/M/Y",strtotime($RO->registered_on)) : '<span class="glyphicon glyphicon-remove"></span>' !!}</td>
								<td>{{ ($RO->presale_enddate) ? date("d/M/Y",strtotime($RO->presale_enddate)) : '' }}</td>
								<td>{{ ($RO->presale_extended_to) ? date("d/M/Y",strtotime($RO->presale_extended_to)) : '' }}</td>
								<td>{{ ($RO->presale_extended_to && $RO->Extender) ? $RO->Extender->name : '' }}</td>
								<td>@php
									$DATE = (($RO->presale_extended_to)?:$RO->presale_enddate)?:false;
									if($DATE){
										$DT = strtotime($DATE . " 23:59:59");
										if(time() >= $DT) echo "Expired/Inactive";
										else echo "Active";
									}
								@endphp</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	
</div>

@endsection