@extends("stp.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Distributors</strong></div>
		<div class="panel-body">
			<div class="clearfix pagination">
				<div class="pull-left col-xs-4 p0"><div class="input-group"><input type="text" name="search_text" class="form-control" placeholder="Search by name, email, phone" value="{{ Request()->search_text }}"><a href="javascript:SearchDistributors()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
				<div class="pull-right">{!! $Pagination !!}</div>
			</div>
			<div class="table table-responsive">
				<table class="table table-bordered">
					<thead><tr><th>No</th><th>Name</th><th>Address</th><th>Action</th></tr></thead>
					<tbody>
					@if($Data->isNotEmpty())
					@foreach($Data as $Line)
						<tr>
							<td>{{ $loop->iteration }}</td>
							<td>{{ $Line->name }}</td>
							<td>{!! PartnerDetails($Line) !!}</td>
							<td nowrap>{!! PartnerActions($Line) !!}</td>
						</tr>
					@endforeach
					@else
						<tr><td colspan="4"><div class="jumbotron text-center no-record"><h3>No Records found</h3></div></td></tr>
					@endif
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection
@php
function PartnerDetails($P){
	$A = PartnerAddress($P->Details);
	$B = PartnerContact($P);
	return implode("<br>",[$A,$B]);
}
function PartnerAddress($D){
	$S = ', ';
	$Adr = [$D->address1,$D->address2];
	if($D->city) array_push($Adr,'<br>'.$D->City->name,$D->City->State->name,$D->City->State->Country->name);
	return implode($S, $Adr);
}
function PartnerContact($P){
	$No = ['+',$P->Details->phonecode,'-',$P->Details->phone];
	$Emails = $P->Logins->pluck('email')->toArray();
	return ' ' . join('',$No) . '<br/>' . ' ' . join('<br/>', $Emails);
}
function PartnerActions($P){
	return implode("",[
		glyLink('javascript:ResetDistributorLogin(\''.$P->code.'\',\''. $P->name .'\',\''. DistributorEmail($P->Logins) .'\')','Send login reset mail to '.$P->name,'log-in',['class'=>'btn btn-none']),
		glyLink('javascript:SendProductInformation(\''.$P->code.'\',\''. $P->name .'\',\''. DistributorEmail($P->Logins) .'\')','Send Product Details and Download links to '.$P->name,'share-alt',['class'=>'btn btn-none']),
		glyLink('javascript:SendProductUpdates(\''.$P->code.'\',\''. $P->name .'\',\''. DistributorEmail($P->Logins) .'\')','Send product\'s latest update details to '.$P->name,'send',['class'=>'btn btn-none']),
		glyLink(Route('stp.distributor.edit',['code'=>$P->code]),'Edit details of '.$P->name,'edit',['class'=>'btn btn-none']),
	]);
}
function DistributorEmail($L){
	$LA = $L->toArray();
	foreach($LA as $LD){
		if(in_array('distributor',array_column($LD['roles'],'rolename')));
			return $LD['email'];
	}
}
@endphp
@push('css')
<style type="text/css">
	.pagination { margin: 0px !important; }
	.p0 { padding: 0px !important; }
</style>
@endpush
@push('js')
<script type="text/javascript" src="js/stp_distributor.js"></script>
@endpush