@extends("tst.page")
@include('BladeFunctions')
@section("content")
@php
$Team = \App\Models\SupportTeam::with(['Tickets' => function($Q){ $Q->with(['Customer','Product','Edition','CreatedBy','Tasks' => function($Q){ $Q->with('Ticket','Responder'); },'Cstatus.User']); }])->first();
$TicketStatusReport = GetTicketStatusReport($Team->Tickets);
$Tasks = $Team->Tickets->map(function($item){ return $item->Tasks; })->flatten();
$TaskStatusReport = GetTaskStatusReport($Tasks);
$ActiveTasks = $Tasks->filter(function($item){ return in_array($item->Cstatus->status,['WORKING','HOLD']); });
//dd($Activities)
@endphp

<div class="content">
	@include('notification.notifications')
	<div class="row">
		<div class="col col-md-12">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">New Tickets</div></div><div class="panel-body">
			@component('tkt.comp_tickets_with_action',['Tickets' => $Team->Tickets->filter(function($item){ return in_array($item->Cstatus->status,['NEW','REOPENED']); })]) @endcomponent
			</div></div>
		</div>
		<div class="col col-md-7">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Agent Wise Active Tasks</div></div><div class="panel-body" style="font-size: 12px">
			@component('tsk.comp_agent_wise_activities',['Agents' => \App\Models\TechnicalSupportAgent::all(), 'Tasks' => $ActiveTasks]) @endcomponent
			</div></div>
		</div>
		<div class="col col-md-5" style="padding-left: 0px;">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Service Requests</div></div><div class="panel-body">
			@component('sreq.comp_list_with_action',['sregs' => \App\Models\ServiceRequest::where('status','ACTIVE')->get()]) @endcomponent
			</div></div>
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Category Wise Tickets</div><a href="{{ Route('category.report') }}" class="btn btn-xs btn-default pull-right" style="margin-top: -22px">View detail report</a></div><div class="panel-body" style="font-size: 12px">
			@component('tkt.comp_category_wise_active_tickets',['Tickets' => $Team->Tickets->filter(function($item){ return !in_array($item->Cstatus->status,['CLOSED','COMPLETED','RECREATED','DISMISSED']); })]) @endcomponent
			</div></div>
		</div>
	</div>
	<div class="panel panel-default tkt_sd"><div class="panel-heading"><div class="panel-title">Ticket Status Details</div></div><div class="panel-body"><div class="row">
		<div class="col col-md-5"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Current Status</div></div><div class="panel-body"><table class="table table-bordered"><thead><tr><th>New</th><th>Opened</th><th>Inprogress</th><th>Holded</th></tr></thead><tbody>
			<tr style="text-align: center"><td class="cst_new"></td><td class="cst_opened"></td><td class="cst_inprogress"></td><td class="cst_holded"></td></tr>
		</tbody></table></div></div></div>
		<div class="col col-md-7" style="padding-left: 0px;"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Summary</div><select class="form-control pull-right" name="period" onchange="PopulateTicketSummary(this.value)" style="width: 100px; margin-top: -28px; padding: 0px;">{!! GetPeriodOptions() !!}</select></div><div class="panel-body"><table class="table table-bordered"><thead><tr><th>Total</th><th>Closed</th><th>New/Opened</th><th>Inprogress</th><th>Holded</th><th>Repened</th></tr></thead><tbody>
			<tr style="text-align: center"><td class="ts_total"></td><td class="ts_closed"></td><td class="ts_new"></td><td class="ts_inprogress"></td><td class="ts_hold"></td><td class="ts_reopened"></td></tr>
		</tbody></table></div></div></div>
	</div></div></div>
	<div class="row">
		<div class="col col-md-6">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Active Tickets</div></div><div class="panel-body" style="font-size: 14px;">
			@component('tkt.comp_active_tickets',['Tickets' => $Team->Tickets->filter(function($item){ return in_array($item->Cstatus->status,['INPROGRESS','HOLD']); })]) @endcomponent
			</div></div>
		</div>
		<div class="col col-md-6" style="padding-left: 0px;">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Active Tasks</div></div><div class="panel-body" style="font-size: 14px;">
			@component('tsk.comp_active_tasks_small',['Tasks' => $ActiveTasks]) @endcomponent
			</div></div>
		</div>
	</div>
	<!--<div class="panel panel-default tsk_sd"><div class="panel-heading"><div class="panel-title">Task Status Details</div></div><div class="panel-body"><div class="row">
		<div class="col col-md-5"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Current Status of Tasks</div></div><div class="panel-body"><table class="table table-bordered"><thead><tr><th>New</th><th>Opened/Working</th><th>Holded</th></tr></thead><tbody>
			<tr style="text-align: center"><td class="cst_new"></td><td class="cst_working"></td><td class="cst_hold"></td></tr>
		</tbody></table></div></div></div>
		<div class="col col-md-7" style="padding-left: 0px;"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Tasks Summary</div><select class="form-control pull-right" name="period" onchange="PopulateTaskSummary(this.value)" style="width: 100px; margin-top: -28px; padding: 0px;">{!! GetPeriodOptions() !!}</select></div><div class="panel-body"><table class="table table-bordered"><thead><tr><th>Total</th><th>Closed</th><th>New</th><th>Working</th><th>Holded</th><th>Rechecked</th></tr></thead><tbody>
			<tr style="text-align: center"><td class="ts_total"></td><td class="ts_closed"></td><td class="ts_new"></td><td class="ts_working"></td><td class="ts_hold"></td><td class="ts_recheck"></td></tr>
		</tbody></table></div></div></div>
	</div></div></div>-->

</div>


@endsection
@php
function GetTicketStatusReport($Tkt){
	$Status = []; if($Tkt->isEmpty()) return [];
	$Tkt->each(function($item, $key)use(&$Status){
		if(!array_key_exists($item->Cstatus->status,$Status)) $Status[$item->Cstatus->status] = [];
		if(!array_key_exists($item->code,$Status[$item->Cstatus->status])) $Status[$item->Cstatus->status][$item->code] = strtotime($item->created_at);
	});
	return $Status;
}
function GetTaskStatusReport($Tsk){
	$Status = []; if($Tsk->isEmpty()) return [];
	$Tsk->each(function($item, $key)use(&$Status){
		if(!array_key_exists($item->Cstatus->status,$Status)) $Status[$item->Cstatus->status] = [];
		if(!array_key_exists($item->id,$Status[$item->Cstatus->status])) $Status[$item->Cstatus->status][$item->id] = strtotime($item->created_at);
	});
	return $Status;
}
function GetPeriodOptions(){
	$opts = [strtotime(date('Y-m-d 00:00:00')) => 'Today', strtotime(date('Y-m-d 00:00:00',strtotime('-1 day'))) => 'From yesterday', strtotime(date('Y-m-d 00:00:00',strtotime('-2 day'))) => 'Last 2 Days', strtotime(date('Y-m-d 00:00:00',strtotime('-6 day'))) => 'Last 6 Days', strtotime(date('Y-m-d 00:00:00',strtotime('-'.date('w').' days'))) => 'This Week' , strtotime(date('Y-m-d 00:00:00',strtotime('-29 days'))) => 'Last 30 Days', strtotime(date('Y-m-01 00:00:00')) => 'This Month', strtotime(date('Y-m-01 00:00:00',strtotime('-1 month'))) . '&' . strtotime(date('Y-m-t 00:00:00',strtotime('-1 month'))) => 'Previous Month', strtotime(date('Y-m-d 00:00:00',strtotime('-89 days'))) => 'From last 90 Days', strtotime(date('Y-m-d 00:00:00',strtotime('-364 days'))) => 'From Last 365 Days', strtotime(date('Y-01-01 00:00:00')) => 'This Year', strtotime(date('Y-01-01 00:00:00',strtotime('-1 year'))) => 'Previous Year', 1 => 'Total'];
	$optsArray = [];
	array_walk($opts,function($itm, $key)use(&$optsArray){
		$optsArray[] = '<option value="'.($key).'">'.$itm.'</option>';
	});
	return implode('',$optsArray);
}
function GetTaskSummaryPeriodOptions(){
	$opts = [1 => 'Total',strtotime(date('Y-m-d 00:00:00')) => 'Today', strtotime(date('Y-m-d 00:00:00',strtotime('-1 day'))) => 'From yesterday', strtotime(date('Y-m-d 00:00:00',strtotime('-2 day'))) => 'Last 2 Days', strtotime(date('Y-m-d 00:00:00',strtotime('-6 day'))) => 'Last 6 Days', strtotime(date('Y-m-d 00:00:00',strtotime('-'.date('w').' days'))) => 'This Week' , strtotime(date('Y-m-d 00:00:00',strtotime('-29 days'))) => 'Last 30 Days', strtotime(date('Y-m-01 00:00:00')) => 'This Month', strtotime(date('Y-m-01 00:00:00',strtotime('-1 month'))) . '&' . strtotime(date('Y-m-t 00:00:00',strtotime('-1 month'))) => 'Previous Month', strtotime(date('Y-m-d 00:00:00',strtotime('-179 days'))) => '6 Months', strtotime(date('Y-m-d 00:00:00',strtotime('-364 days'))) => 'Year'];
	$optsArray = [];
	array_walk($opts,function($itm, $key)use(&$optsArray){
		$optsArray[] = '<option value="'.($key).'">'.$itm.'</option>';
	});
	return implode('',$optsArray);
}

@endphp
@push('js')
<script type="text/javascript">
var _TicketStatus = {!! json_encode($TicketStatusReport) !!};
function PopulateTicketCurrentStatus(){
	$.each({'NEW':'new','OPENED':'opened','INPROGRESS':'inprogress','HOLD':'holded'},function(S,C){ $('td.cst_'+C,$('.tkt_sd')).html(TicketListAnchor(_TicketStatus[S]?Object.keys(_TicketStatus[S]).length:0,S,null)); })
}
function PopulateTicketSummary(T){ T = T+"";
	$('td[class^="ts_"]',$('.tkt_sd')).text(0); TDTtl = $('td.ts_total',$('.tkt_sd')).text(0);
	P = parseInt((new Date()).getTime()/1000)+3600; if(T.indexOf('&') > -1) P = T.split('&')[1]; T = T.split('&')[0];
	$.each({'NEW':'new','OPENED':'new','INPROGRESS':'inprogress','CLOSED':'closed','COMPLETED':'closed','HOLD':'hold','REOPENED':'closed','RECREATED':'reopened'},function(S,C){
		TD = $('td.ts_'+C,$('.tkt_sd')); TDTtl = $('td.ts_total',$('.tkt_sd'));
		Ttl = (typeof _TicketStatus[S] == 'undefined') ? 0 : Object.values(_TicketStatus[S]).filter(t => parseInt(t) >= parseInt(T)).filter(t => parseInt(t) < parseInt(P)).length
		TDT = TD.text()?parseInt(TD.text()):0; TD.html(TicketListAnchor(TDT+=Ttl,C,T,P));
		NT = TDTtl.text()?parseInt(TDTtl.text()):0; TDTtl.text(NT+=Ttl);
	})
}
function TicketListAnchor(C,S,P,T){
	A = $('<a>').attr({ target:'_blank', href:TicketListUrl(S,P,T) }).text(C).css({ color:'inherit' })
	return A[0];
}
function TicketListUrl(S,P,T){
	Url = '{{ Route("tickets.list",[$Team->code,"--status--"]) }}'.replace("--status--",S);
	return (P || P == 0) ? Url+"?period="+P+'&till='+T : Url;
}
$(function(){
	PopulateTicketCurrentStatus()
	PopulateTicketSummary({{ strtotime(date('Y-m-d 00:00:00')) }})
})
</script>
<script type="text/javascript">
var _TaskStatus = {!! json_encode($TaskStatusReport) !!};
function PopulateTaskCurrentStatus(){
	$.each({'CREATED':'new','ASSIGNED':'new','OPENED':'working','RECHECK':'new','REASSIGNED':'new','WORKING':'working','HOLD':'hold','CLOSED':'closed'},function(S,C){ $('td.cst_'+C,$('.tsk_sd')).html(TaskListAnchor(_TaskStatus[S]?Object.keys(_TaskStatus[S]).length:0,S,null)); })
}
function PopulateTaskSummary(T){ T = T+"";
	$('td[class^="ts_"]',$('.tsk_sd')).text(0); TDTtl = $('td.ts_total',$('.tsk_sd')).text(0);
	P = parseInt((new Date()).getTime()/1000)+3600; if(T.indexOf('&') > -1) P = T.split('&')[1]; T = T.split('&')[0];
	$.each({'CREATED':'new','ASSIGNED':'new','OPENED':'working','RECHECK':'recheck','REASSIGNED':'new','WORKING':'working','HOLD':'hold','CLOSED':'closed'},function(S,C){
		TD = $('td.ts_'+C,$('.tsk_sd')); TDTtl = $('td.ts_total',$('.tsk_sd'));
		Ttl = (typeof _TaskStatus[S] == 'undefined') ? 0 : Object.values(_TaskStatus[S]).filter(t => parseInt(t) >= parseInt(T)).filter(t => parseInt(t) < parseInt(P)).length
		TDT = TD.text()?parseInt(TD.text()):0; TD.html(TaskListAnchor(TDT+=Ttl,C,T,P));
		NT = TDTtl.text()?parseInt(TDTtl.text()):0; TDTtl.text(NT+=Ttl);
	})
}
function TaskListAnchor(C,S,P,T){
	A = $('<a>').attr({ target:'_blank', href:TaskListUrl(S,P,T) }).text(C).css({ color:'inherit' })
	return A[0];
}
function TaskListUrl(S,P,T){
	Url = '{{ Route("tasks.list",["agcode","--status--"]) }}'.replace("--status--",S);
	return (P || P == 0) ? Url+"?period="+P+'&till='+T : Url;
}
$(function(){
	PopulateTaskCurrentStatus()
	PopulateTaskSummary({{ strtotime(date('Y-m-d 00:00:00')) }})
})
</script>
@endpush