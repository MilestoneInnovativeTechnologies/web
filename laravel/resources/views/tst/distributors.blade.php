@extends("tst.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Distributors</strong>{!! PanelHeadBackButton(Route('tst.index')) !!}<span class="pull-right"> &nbsp; </span>{!! PanelHeadAddButton(Route('tst.distributors.assign',['code' =>	$Code]),'Assign/Unassign Distributors') !!}</div>
		<div class="panel-body">@if($Data->count())
			<div class="table-responsive">
				<table class="table table-bordered">
					<thead><tr><th>No</th><th>Distributor</th><th>Address</th><th>Contact</th></tr></thead>
					<tbody>@foreach($Data->Distributors as $Dist)
						<tr>
							<td>{{ $loop->iteration }}</td>
							<td>{{ $Dist->Partner->name }}</td>
							<td>{{ PartnerAddress($Dist->Partner->Details) }}</td>
							<td>{!! PartnerContact($Dist->Partner->Details, $Dist->Partner->Logins) !!}</td>
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

function PartnerAddress($Dts){
	$AdrsAry = [$Dts->address1, $Dts->address2];
	if($Dts->city) array_push($AdrsAry,$Dts->City->name, $Dts->City->State->name, $Dts->City->State->Country->name);
	return implode(", ", array_filter($AdrsAry));
}

function PartnerContact($Dts, $Lgs){
	$CntAry = ['+',$Dts->phonecode,'-',$Dts->phone];
	$Email = $Lgs->pluck('email')->toArray();
	return implode('<br/>',array_merge([implode('',$CntAry)],$Email));
}

@endphp