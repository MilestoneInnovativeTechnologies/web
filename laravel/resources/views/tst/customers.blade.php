@extends("tst.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Customers</strong>{!! PanelHeadBackButton(Route('tst.index')) !!}<span class="pull-right"> &nbsp; </span>{!! PanelHeadAddButton(Route('tst.customers.assign',['code' =>	$Code]),'Assign/Unassign Customers') !!}</div>
		<div class="panel-body">@if($Data->count())
			<div class="clearfix pagination">
				<div class="pull-left col-xs-4 p0"><div class="input-group"><input type="text" name="search_text" class="form-control" placeholder="Search" value="{{ Request()->search_text }}"><a href="javascript:Search()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
				<div class="pull-right">{!! $Pagination !!}</div>
			</div>
			<div class="table-responsive">
				<table class="table table-bordered">
					<thead><tr><th>No</th><th>Customer</th><th>Distributor</th><th>Product</th><th>Edition</th><th>Address</th><th>Contact</th></tr></thead>
					<tbody>@foreach($Data as $Cust)
						<tr>
							<td>{{ $loop->iteration }}</td>
							<td>{{ $Cust->Partner->name }}</td>
							<td>{{ Partnerdisributor($Cust->Distributor,$Cust->Dealer) }}</td>
							<td>{{ $Cust->Product->name }}</td>
							<td>{{ $Cust->Edition->name }}</td>
							<td>{{ PartnerAddress($Cust->Partner->Details) }}</td>
							<td>{!! PartnerContact($Cust->Partner->Details, $Cust->Partner->Logins) !!}</td>
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

function Partnerdisributor($DST, $DLR){
	if($DST->Roles->isNotEmpty()) return $DST->ParentDetails->name;
	return $DLR->Parent1->ParentDetails->name;
}

@endphp
@push('css')
<style type="text/css">
	.pagination { margin: 0px !important; }
	.p0 { padding: 0px !important; }
</style>
@endpush
@push('js')
<script type="text/javascript">
	function Search(){
		st = $('[name="search_text"]').val();
		//if(st == "") return;
		location.search = '?page=1&search_text='+st
	}
</script>
@endpush