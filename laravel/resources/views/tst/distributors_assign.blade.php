@extends("tst.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Assigned Distributors</strong> <a href="javascript:InvertSelection('selfassigned')" class="btn-link btn-sm">invert selection</a> {!! PanelHeadBackButton(Route('tst.distributors',['code'=>$Code])) !!}</div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-bordered selfassigned">
					<thead><tr><th>No</th><th>Distributor</th><th>Address</th><th>Contact</th></tr></thead>
					<tbody>@foreach($SelfAssigned as $SA)
						<tr>
							<td class="text-center"><input type="checkbox" name="distributor[]" value="{{ $SA->Partner->code }}" checked></td>
							<td>{{ $SA->Partner->name }}</td>
							<td>{{ PartnerAddress($SA->Partner->Details) }}</td>
							<td>{!! PartnerContact($SA->Partner->Details, $SA->Partner->Logins) !!}<input type="hidden" name="old_dist[]" value="{{ $SA->Partner->code }}"></td>
						</tr>
					@endforeach</tbody>
				</table>
			</div>
		</div>
		<div class="panel-heading"><strong>Unassigned Distributors</strong> <a href="javascript:InvertSelection('unassigned')" class="btn-link btn-sm">invert selection</a></div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-bordered unassigned">
					<thead><tr><th>No</th><th>Distributor</th><th>Address</th><th>Contact</th></tr></thead>
					<tbody>@foreach($Unassigned as $UA)
						<tr>
							<td class="text-center"><input type="checkbox" name="distributor[]" value="{{ $UA->code }}"></td>
							<td>{{ $UA->name }}</td>
							<td>{{ PartnerAddress($UA->Details) }}</td>
							<td>{!! PartnerContact($UA->Details, $UA->Logins) !!}</td>
						</tr>
					@endforeach</tbody>
				</table>
			</div>
		</div>
		<div class="panel-heading"><strong>Other's Assigned Distributors</strong> <a href="javascript:InvertSelection('otherassigned')" class="btn-link btn-sm">invert selection</a></div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-bordered otherassigned">
					<thead><tr><th>No</th><th>Distributor</th><th>Current Support Team</th><th>Address</th><th>Contact</th></tr></thead>
					<tbody>@foreach($OthersAssigned as $OA)
						<tr>
							<td class="text-center"><input type="checkbox" name="distributor[]" value="{{ $OA->Partner->code }}"></td>
							<td>{{ $OA->Partner->name }}</td>
							<td>{{ $OA->Team->name }}</td>
							<td>{{ PartnerAddress($OA->Partner->Details) }}</td>
							<td>{!! PartnerContact($OA->Partner->Details, $OA->Partner->Logins) !!}</td>
						</tr>
					@endforeach</tbody>
				</table>
			</div>
		</div>
		<div class="panel-footer clearfix">
			<a href="javascript:UpdateDistributors();" class="btn btn-primary pull-right">Update</a>
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
@push('js')
<script type="text/javascript">
	function InvertSelection(cls){
		tbl = $('table.'+cls);
		$('input[type="checkbox"]',tbl).each(function(i,cbx){
			$(cbx).prop('checked',!$(cbx).prop('checked'))
		})
	}
	function UpdateDistributors(){
		FireAPI('api/v1/tst/action/ud/{{$Code}}',function(R){
			alert('Updated Succesfully..');
			location.reload();
		},$('input[type="checkbox"],input[name="old_dist[]"]').serializeArray());
	}
</script>
@endpush