@extends("customer.dashboardpage")
@include('BladeFunctions')
@section("content")
@php
$Registrations = \App\Models\CustomerRegistration::own()->with(['Customer','Product','Edition'])->get();
$Unregs = $Registrations->filter(function($itm){ return(is_null($itm->key) || is_null($itm->serialno) || is_null($itm->registered_on));  });
$Regs = $Registrations->filter(function($itm){ return(!is_null($itm->key) && !is_null($itm->serialno) && !is_null($itm->registered_on));  });
$PV = \App\Models\PackageVersion::status('APPROVED')->oldest('version_sequence')->whereHas('Package',function($Q){ $Q->whereType('Update'); })->get()->keyBy(function($itm){ return implode("-",[$itm->product,$itm->edition]); });
//dd($PV);
//dd(\App\Models\CustomerPrintObject::all()->toArray());
@endphp

<div class="content dashboard">
	@include('notification.notifications')
	<div class="row">@if($Unregs->isNotEmpty())
		<div class="col-md-12">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Unregistered Products</div></div><div class="panel-body">
			@component('crd.comp_unregistered',['Data' => $Unregs]) @endcomponent
			</div></div>
		</div>@endif
		<div class="col-md-8">
			<div class="col-xs-6" style="padding-left: 0px">
				<div class="panel panel-default"><div class="panel-heading"><span class="panel-title">Print Objects</span>{!! PanelHeadButton(Route('cpo.index'),'Browse all','share-alt','default','xs') !!}</div><div class="panel-body" style="font-size: 12px">
                @component('cpo.comp_printobjects',['PrintObjects' => \App\Models\CustomerPrintObject::take(3)->get()]) @endcomponent
				</div></div>
			</div>
			
			<div class="col-xs-6" style="padding-left: 0px; padding-right: 0px">
				<div class="panel panel-default"><div class="panel-heading"><span class="panel-title">Database Backups</span>{!! PanelHeadButton(Route('dbb.upload'),'Upload New','upload','default','xs') !!}</div><div class="panel-body" style="font-size: 12px">
                @component('dbb.comp_lists_sm',['Dbbs' => \App\Models\DatabaseBackup::all()]) @endcomponent
				</div></div>
			</div>
			
		</div>
		<div class="col-md-4" style="padding-left: 0px">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Updates</div></div><div class="panel-body">
				<div class="table-responsive"><table class="table table-striped">
				<tbody>@forelse($Registrations as $Reg)
					<tr><th colspan="2" class="text-center">{{ $Reg->Product->name }} {{ $Reg->Edition->name }} Edition</th></tr>
					<tr><td class="text-center">Using Version</td><td class="text-center">Latest Version</td></tr>
					<tr><th class="text-center" style="vertical-align: middle">{{ $Reg->version }}</th><th class="text-center"><a class="btn btn-info btn-xs" href="{{ Route('software.download',ProductDownloadKey($Reg,$PV[$Reg->product.'-'.$Reg->edition])) }}">Download {{ $PV[$Reg->product.'-'.$Reg->edition]->version_numeric }}</a></th></tr>
					@if($loop->remaining) <tr><td colspan="2">&nbsp;</td></tr> @endif
				@empty
					<tr><th>No Products</th></tr>
				@endforelse</tbody></table></div>
			</div></div>
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Registered Products</div></div><div class="panel-body">
				<div class="table-responsive"><table class="table table-striped">
				<tbody>@forelse($Regs as $Reg)
					<tr><th colspan="2">{{ $Reg->Product->name }} {{ $Reg->Edition->name }} Edition</th></tr>
					<tr><th>Serial</th><td>{{ $Reg->serialno }}</td></tr>
					<tr><th>Key</th><td>{{ $Reg->key }}</td></tr>
					@if($loop->remaining) <tr><td colspan="2">&nbsp;</td></tr> @endif
				@empty
					<tr><th>No Products</th></tr>
				@endforelse</tbody></table></div>
			</div></div>
		</div>
		<div class="col-md-12">
			<div class="panel panel-default"><div class="panel-heading"><span class="panel-title">Active Tickets</span></div><div class="panel-body">
			@component('tkt.comp_tickets_customer_with_action',['Tickets' => \App\Models\Ticket::whereHas('Cstatus',function($Q){ $Q->whereNotIn('status',['CLOSED','COMPLETED','RECREATED','DISMISSED']); })->get()]) @endcomponent
			</div></div>
		</div>
	</div>
	
</div>

@endsection
@php
function ProductDownloadKey($Reg,$PV){
	return \App\Http\Controllers\KeyCodeController::Encode(['customer','product','edition','package','version','expiry','customer_download'],[$Reg->customer,$Reg->product,$Reg->edition,$PV->package,$PV->version_numeric,strtotime("+10 minutes"),'yes']);
}
@endphp