@extends("tsa.page")
@include('BladeFunctions')
@section("content")
@php
$Agent = \App\Models\TechnicalSupportAgent::with(['Details','Distributor','Tasks' => function($Q){ $Q->with(['CreatedBy','Responder.Assigner','Ticket' => function($Q){ $Q->with('CreatedBy','Customer'); }]); }])->first();
$StatusReport = GetStatusReport($Agent->Tasks);
//dd(GetStatusReport($Agent->Tasks))
@endphp

<div class="content">
	@include('notification.notifications')
	<div class="row">
		<div class="col col-md-5"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Current Status of Tasks</div></div><div class="panel-body"><table class="table table-bordered"><thead><tr><th>New</th><th>Opened/Working</th><th>Holded</th></tr></thead><tbody>
			<tr style="text-align: center"><td class="cst_new"></td><td class="cst_working"></td><td class="cst_hold"></td></tr>
		</tbody></table></div></div></div>
		<div class="col col-md-7" style="padding-left: 0px;"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Tasks Summary</div><select class="form-control pull-right" name="period" onchange="PopulateTaskSummary(this.value)" style="width: 100px; margin-top: -28px; padding: 0px;">{!! GetTaskSummaryPeriodOptions() !!}</select></div><div class="panel-body"><table class="table table-bordered"><thead><tr><th>Total</th><th>Closed</th><th>New</th><th>Working</th><th>Holded</th><th>Rechecked</th></tr></thead><tbody>
		<tr style="text-align: center"><td class="ts_total"></td><td class="ts_closed"></td><td class="ts_new"></td><td class="ts_working"></td><td class="ts_hold"></td><td class="ts_recheck"></td></tr>
		</tbody></table></div></div></div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Holded Tasks</div></div><div class="panel-body">
			@component('tsk.comp_agent_tasks',['Tasks' => $Agent->Tasks->filter(function($Item){ return $Item->Cstatus->status == 'HOLD'; })]) @endcomponent
			</div></div>
		</div>
		<div class="col-md-6" style="padding-left: 0px;">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Working Tasks</div></div><div class="panel-body">
			@component('tsk.comp_agent_tasks',['Tasks' => $Agent->Tasks->filter(function($Item){ return $Item->Cstatus->status == 'WORKING'; })]) @endcomponent
			</div></div>
		</div>
		<div class="col-md-9">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">New Tasks</div></div><div class="panel-body">
			@component('tsk.comp_detail_tasks',['Tasks' => $Agent->Tasks->filter(function($Item){ return in_array($Item->Cstatus->status,['ASSIGNED','OPENED','REASSIGNED']); })]) @endcomponent
			</div></div>
		</div>
		<div class="col-md-3" style="padding-left: 0px;">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Service Requests</div></div><div class="panel-body">
			@component('sreq.comp_list_with_action',['sregs' => \App\Models\ServiceRequest::where('status','ACTIVE')->get()]) @endcomponent
			</div></div>
		</div>
		<div class="col-md-6">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">General Uploads</div></div><div class="panel-body">
			@component('gu.comp_generaluploads',['Forms' => \App\Models\GeneralUpload::where('created_by',$Agent->code)->take(5)->get()]) @endcomponent
			</div></div>
		</div>
		<div class="col-md-6">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Database Backups</div></div><div class="panel-body">
			@component('dbb.comp_lists',['Dbbs' => \App\Models\DatabaseBackup::where('status','WITHIN')->take(5)->get()]) @endcomponent
			</div></div>
		</div>
	</div>
</div>


@endsection
@php
function GetStatusReport($Tsk){
	$Status = []; if($Tsk->isEmpty()) return [];
	$Tsk->each(function($item, $key)use(&$Status){
		if(!array_key_exists($item->Cstatus->status,$Status)) $Status[$item->Cstatus->status] = [];
		if(!array_key_exists($item->id,$Status[$item->Cstatus->status])) $Status[$item->Cstatus->status][$item->id] = strtotime($item->created_at);
	});
	return $Status;
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
var _Status = {!! json_encode($StatusReport) !!};
function PopulateCurrentStatus(){
	$.each({'CREATED':'new','ASSIGNED':'new','OPENED':'working','RECHECK':'new','REASSIGNED':'new','WORKING':'working','HOLD':'hold','CLOSED':'closed'},function(S,C){ $('td.cst_'+C).html(TaskListAnchor(_Status[S]?Object.keys(_Status[S]).length:0,S,null)); })
}
function PopulateTaskSummary(T){ T = T+"";
	$('td[class^="ts_"]').text(0); TDTtl = $('td.ts_total').text(0);
	P = parseInt((new Date()).getTime()/1000)+3600; if(T.indexOf('&') > -1) P = T.split('&')[1]; T = T.split('&')[0];
	$.each({'CREATED':'new','ASSIGNED':'new','OPENED':'working','RECHECK':'recheck','REASSIGNED':'new','WORKING':'working','HOLD':'hold','CLOSED':'closed'},function(S,C){
		TD = $('td.ts_'+C); TDTtl = $('td.ts_total');
		Ttl = (typeof _Status[S] == 'undefined') ? 0 : Object.values(_Status[S]).filter(t => parseInt(t) >= parseInt(T)).filter(t => parseInt(t) < parseInt(P)).length
		TDT = TD.text()?parseInt(TD.text()):0; TD.html(TaskListAnchor(TDT+=Ttl,C,T,P));
		NT = TDTtl.text()?parseInt(TDTtl.text()):0; TDTtl.text(NT+=Ttl);
	})
}
function TaskListAnchor(C,S,P,T){
	A = $('<a>').attr({ target:'_blank', href:TaskListUrl(S,P,T) }).text(C).css({ color:'inherit' })
	return A[0];
}
function TaskListUrl(S,P,T){
	Url = '{{ Route("tasks.list",[$Agent->code,"--status--"]) }}'.replace("--status--",S);
	return (P || P == 0) ? Url+"?period="+P+'&till='+T : Url;
}
$(function(){
	PopulateCurrentStatus()
	PopulateTaskSummary(1)
})
</script>
@endpush