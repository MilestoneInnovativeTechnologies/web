@extends("customer.page")
@include('BladeFunctions')
@section("content")
@php
$Customer = \App\Models\Customer::whereCode(Request()->code)->with(['Details.Industry','ParentDetails','Registration','Cookies','Printobjects','Connections','Supportteam','Tickets' => function($Q){ $Q->with(['Product','Edition','Cstatus']); },'Forms','Backups'])->first();
//dd($Customer->toArray());
@endphp

<div class="content">
	<div class="row">
		<div class="col col-md-9">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">{{ $Customer->name }}</div></div><div class="panel-body">
				<div class="table table-responsive"><table class="table striped"><tbody>
					<tr><th>Code</th><th>:</th><td>{{ $Customer->code }}</td><th>Industry</th><th>:</th><td>{{ ($Customer->Details->industry)?($Customer->Details->Industry->name):'' }}</td></tr>
					<tr><th>Name</th><th>:</th><td>{{ $Customer->name }}</td><th>Status</th><th>:</th><td>{{ $Customer->status }}</td></tr>
					<tr><th>Address</th><th>:</th><td>{!! PartnerAddress($Customer->Details) !!}</td><th>Status Description</th><th>:</th><td>{!! nl2br($Customer->status_description) !!}</td></tr>
					<tr><th>Phone</th><th>:</th><td>{!! PartnerPhone($Customer->Details) !!}</td><th>Website</th><th>:</th><td>{{ $Customer->website }}</td></tr>
					<tr><th>Email</th><th>:</th><td>{!! PartnerEmails($Customer->Logins) !!}</td><th>&nbsp;</th><th> </th><td>&nbsp;</td></tr>
				</tbody></table></div>
				<div class="table table-responsive"><table class="table striped"><tbody>
					@if($Dealer = getParent($Customer->ParentDetails[0],'dealer'))<tr><th>Dealer</th><th>:</th><td><a href="{{ Route(((session()->get('_company'))?'mit.':'').'dealer.panel',$Dealer->code) }}" style="text-decoration: none; color: inherit">{{ $Dealer->name }}</a></td><th>&nbsp;</th><th>&nbsp;</th><td>&nbsp;</td></tr>@endif
					@if($Dist = getParent($Customer->ParentDetails[0],'distributor'))<tr><th>Distributor</th><th>:</th><td><a href="{{ Route('distributor.panel',$Dist->code) }}" style="text-decoration: none; color: inherit">{{ $Dist->name }}</a></td><th>&nbsp;</th><th>&nbsp;</th><td>&nbsp;</td></tr>@endif
				</tbody></table></div>				
			</div></div>
		</div>
		<div class="col col-md-3" style="padding-left: 0px;">
			<div class="panel panel-default">
				<div class="panel-heading"><div class="panel-title">Recent App usages</div></div>
				<div class="panel-body">@component('log.comp_recentusage') @endcomponent</div>
			</div>
		</div>
	</div>
	<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Products</div></div><div class="panel-body">
	@component('crd.comp_registrations',['Registrations' => $Customer->Registration, 'Supportteams' => $Customer->Supportteam]) @endcomponent
	</div></div>
	<div class="row">
		<div class="col col-md-5"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Customer Cookies</div></div><div class="panel-body">
		@component('tscc.comp_cookies',['Cookies' => $Customer->Cookies]) @endcomponent
		</div></div></div>
		<div class="col col-md-7"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Customer Remote Connections</div></div><div class="panel-body">
		@component('crc.comp_connections',['Connections' => $Customer->Connections]) @endcomponent
		</div></div></div>
	</div>
	<div class="row">
		<div class="col col-md-4"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Customer Print Objects</div></div><div class="panel-body">
		@component('cpo.comp_printobjects',['PrintObjects' => $Customer->Printobjects->chunk(15)->first()]) @endcomponent
		</div></div></div>
		<div class="col col-md-4"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Customer Uploads</div></div><div class="panel-body">
		@component('gu.comp_generaluploads',['Forms' => $Customer->Forms]) @endcomponent
		</div></div></div>
		<div class="col col-md-4"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Database Backups</div></div><div class="panel-body">
		@component('dbb.comp_lists',['Dbbs' => $Customer->Backups]) @endcomponent
		</div></div></div>
	</div>
	<div class="panel panel-default"><div class="panel-heading"><span class="panel-title">Customer Ongoing Tickets</span><a href="{{ Route('customer.tickets',Request()->code) }}" class="btn btn-xs btn-default pull-right">View Customer's all Tickets</a></div><div class="panel-body">
	@component('tkt.comp_tickets',['Tickets' => $Customer->Tickets->filter(function($Item){ return ($Item->Cstatus && !in_array($Item->Cstatus->status,['RECREATED','CLOSED','COMPLETED','DISMISSED'])); })]) @endcomponent
	</div></div>
</div>

@endsection
@php
function PartnerAddress($D){
	$Adr = []; $Loc = []; $Obj = [];
	if($D->address1) $Parts[] = $D->address1; if($D->address2) $Parts[] = $D->address2;
	if(implode(', ',$Adr)) $Obj[] = implode(', ',$Adr);
	if($D->city){
		$Loc[] = $D->City->name; $Loc[] = $D->City->State->name;
		if(implode(', ',$Loc)) $Obj[] = implode(', ',$Loc);
		$Obj[] = $D->City->State->Country->name;
	}
	return trim(implode('<br>',$Obj));
}
function PartnerPhone($D){
	return '+' . $D->phonecode . '-' . $D->phone;
}
function PartnerEmails($L){
	return $L->implode('email',', ');
}
function getParent($P,$R){
	if($P->Roles->contains('name',$R)) return $P;
	if($P->ParentDetails->isNotEmpty()) return getParent($P->ParentDetails[0],$R);
	return null;
}
@endphp