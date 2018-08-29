@extends("crd.page")
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Registration Details</strong></div>
		<div class="panel-body">@if($Registrations->isNotEmpty())
			<div class="table-responsive">
				<table class="table table-bordered">
					<thead><tr><th>No</th><th>Customer</th><th>Product</th><th>Added On</th><th>Registered On</th></tr></thead>
					<tbody>@foreach($Registrations as $reg)
					<tr>
						<td>{{ $loop->iteration }}</td>
						<td><a href="{{ Route('customer.panel',$reg->customer) }}" style="color: inherit">{{ $reg->Customer->name }}</a><br><small><strong>Distributor: </strong><a href="{{ Route('distributor.panel',GetParentOfRole($reg->Customer->ParentDetails[0],'distributor')->code) }}" style="color: inherit">{{ GetParentOfRole($reg->Customer->ParentDetails[0],'distributor')->name }}</a></small></td>
						<td>{{ $reg->Product->name }} {{ $reg->Edition->name }} Edition</td>
						<td>{{ date('D d/M/y h:i A',strtotime($reg->created_at)) }}</td>
						<td>@if($reg->registered_on) {{ date('D d/M/y',strtotime($reg->registered_on)) }}<br><small>({{ $reg->serialno }})</small><br><small>({{ $reg->key }})</small> @else<span class="glyphicon glyphicon-remove"></span>@endif</td>
					</tr>
					@endforeach</tbody>
				</table>
			</div>@else
			<div class="jumbotron">
				<h2 class="text-center">No Records found</h2>
			</div>@endif
		</div>
	</div>
</div>

@endsection
@php
function GetParentOfRole($Parent,$Role){
	if($Parent->Roles->contains('name',$Role)) return $Parent;
	return GetParentOfRole($Parent->ParentDetails[0],$Role);
}
@endphp