@extends("company.page")
@section("content")

<div class="content">
	<div class="clearfix">
		<a href="{{ Route('company.dashboard') }}" class="btn btn-default"><span class="glyphicon glyphicon-arrow-left"></span> Back</a><br><br>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Registration Requests</strong></div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-striped">
					<thead><tr><th>No</th><th>Name</th><th>Product</th><th>Edition</th><th>Distributor</th><th>Requisition No</th><th>Action</th></tr></thead>
					<tbody>@foreach($Data as $Obj)
						<tr>
							<td>{{ $loop->iteration }}</td>
							<td>{{ $Obj['customer']['name'] }}</td>
							<td>{{ $Obj['product']['name'] }}</td>
							<td>{{ $Obj['edition']['name'] }}</td>
							<td>{{ GetDistributorName($Obj['customer']['parent_details'][0]) }}</td>
							<td>{{ $Obj['requisition'] }}</td>
							<td>
								<a href="{{ Route('download.customer.licence',['customer'=>$Obj['customer']['code'],'seqno'=>$Obj['seqno']]) }}" download class="btn" title="Download Licence file"><span class="glyphicon glyphicon-download-alt"></span></a>
								<a href="{{ Route('customer.registration',['code'=>$Obj['customer']['code'],'seqno'=>$Obj['seqno']]) }}" class="btn" title="Enter Registration Key"><span class="glyphicon glyphicon-saved"></span></a>
							</td>
						</tr>
					@endforeach</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection
@php
function GetDistributorName($Parent){
	if(array_contains($Parent['roles'],'name','distributor')) return $Parent['name'];
	return GetDistributorName($Parent['parent_details'][0]);
}
function array_contains($collection,$key,$value){
	if(!is_array($collection)) return false;
	foreach($collection as $ary)
		if(array_key_exists($key,$ary) && $ary[$key] == $value)
			return true;
	return false;
}
@endphp