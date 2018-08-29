@extends("distributor.page_distributor")
@section("content")
@php
$Registrations = \App\Models\CustomerRegistration::own()->with(['Product' => function($Q){ $Q->select('code','name')->withoutGlobalScope('own'); },'Edition' => function($Q){ $Q->select('code','name')->withoutGlobalScope('own'); }])->latest('updated_at')->get();
//dd($Registrations->toArray());
@endphp

<div class="content">
	@include('notification.notifications')
	<div class="row">
		<div class="col col-md-12">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Product Registration Summary</div><select name="reg_sum_period" class="form-control pull-right"  style="width: 100px; margin-top: -28px; padding: 0px;">{!! GetRegistrationSummaryPeriod() !!}</select></div><div class="panel-body">
			@component('crd.comp_registration_summary',['Data' => $Registrations, 'Distributor' => Auth()->user()->partner]) @endcomponent
			</div></div>
		</div>
		<div class="col col-md-6">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Recent Registrations</div></div><div class="panel-body">
			@component('crd.comp_recent',['Data' => $Registrations->filter(function($D){ return ($D->registered_on && $D->key && $D->serialno && strtotime($D->registered_on) > strtotime('-30 days')); })]) @endcomponent
			</div></div>
		</div>
		<div class="col col-md-6" style="padding-left: 0px">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Expiring Customers</div></div><div class="panel-body">
			@component('crd.comp_expiring',['Data' => $Registrations->filter(function($D){ return (!$D->registered_on && (strtotime($D->created_at) > strtotime('-35 days')) && (strtotime('-25 days') > strtotime($D->created_at))); })]) @endcomponent
			</div></div>
		</div>
		<div class="col col-md-8">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Unregistered Recent Customers</div></div><div class="panel-body">
			@component('crd.comp_recent_unregistered',['Data' => $Registrations->filter(function($D){ return (!$D->registered_on && (strtotime($D->created_at) > strtotime('-35 days')) && (strtotime('-25 days') > strtotime($D->created_at))); })]) @endcomponent
			</div></div>
		</div>
		<div class="col col-md-4" style="padding-left: 0px">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Service Requests</div></div><div class="panel-body">
			@component('sreq.comp_list',['sreqs' => \App\Models\ServiceRequest::all()]) @endcomponent
			</div></div>
		</div>
		<div class="col col-md-12">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Active Tickets</div><a href="{{ Route('distributor.tickets') }}" title=" View All Tickets" class="btn btn-default btn-xs pull-right" style="margin-top: -22px;"><span class="glyphicon glyphicon-share-alt"></span>  View All Tickets</a></div><div class="panel-body">
			@component('tkt.comp_tickets',['Tickets' => \App\Models\Ticket::whereHas('Cstatus',function($Q){ $Q->whereNotIn('status',['CLOSED','COMPLETED','RECREATED','DISMISSED']); })->get() ]) @endcomponent
			</div></div>
		</div>
	</div>


</div>

@endsection
@php
function GetRegistrationSummaryPeriod(){
	$opts = [strtotime(date('Y-m-d 00:00:00')) => 'Today', strtotime(date('Y-m-d 00:00:00',strtotime('-'.date('w').' days'))) => 'This Week', strtotime(date('Y-m-d 00:00:00',strtotime('-'.(date('w')+7).' days'))) . '&' . strtotime(date('Y-m-d 00:00:00',strtotime('-'.date('w').' days'))) => 'Previous Week', strtotime(date('Y-m-01 00:00:00')) => 'This Month', strtotime(date('Y-m-01 00:00:00',strtotime('-1 month'))) . '&' . strtotime(date('Y-m-01 00:00:00')) => 'Previous Month', strtotime(date('Y-01-01 00:00:00')) => 'This Year', strtotime(date('Y-m-01 00:00:00',strtotime('-1 year'))) . '&' . strtotime(date('Y-01-01 00:00:00')) => 'Previous Year'];
	$optsArray = [];
	array_walk($opts,function($itm, $key)use(&$optsArray){
		$optsArray[] = '<option value="'.($key).'">'.$itm.'</option>';
	});
	return implode('',$optsArray);
}
@endphp

