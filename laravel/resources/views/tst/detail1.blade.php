@extends("tst.page")
@section("content")
@php
$Team = \App\Models\SupportTeam::whereCode(Request()->code)->with(['Distributors','Agents','Tickets' => function($Q){ $Q->with('Customer','Product','Edition','CreatedBy'); }])->first();
$StatusReport = GetStatusReport($Team->Tickets);
$TAC = \DB::table('v_dailyticketstatus')->get();
@endphp

<div class="content">
	<div class="row">
		<div class="col col-md-9">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">{{ $Team->name }}</div></div><div class="panel-body">
				<div class="table-responsive"><table class="table striped"><tbody>
					<tr><th>Code</th><th>:</th><td>{{ $Team->code }}</td><th>Phone</th><th>:</th><td>{!! PartnerPhone($Team->Details) !!}</td></tr>
					<tr><th>Name</th><th>:</th><td>{{ $Team->name }}</td><th>Email</th><th>:</th><td>{!! PartnerEmails($Team->Logins) !!}</td></tr>
					<tr><th>Address</th><th>:</th><td>{!! PartnerAddress($Team->Details) !!}</td><th>&nbsp;</th><th> </th><td>&nbsp;</td></tr>
					<tr><th>Default Suppport Team</th><th>:</th><td>@if($Team->Defaultst)<strong>YES</strong>@else <strong>NO</strong> @endif</td><th>Website</th><th>:</th><td>{{ $Team->website }}</td></tr>
				</tbody></table></div>
			</div></div>
		</div>
	</div>
	<div class="row">
		<div class="col col-md-12"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Today Activity Count - Agent wise</div></div><div class="panel-body">
		@component('tkt.comp_today_activity_count',['Data' => $TAC,'Team' => Request()->code]) @endcomponent
		</div></div></div>
		<div class="clearfix"></div>
		<div class="col col-md-5"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Current Status of Tickets</div></div><div class="panel-body"><table class="table table-bordered"><thead><tr><th>New</th><th>Opened</th><th>Inprogress</th><th>Holded</th></tr></thead><tbody>
			<tr style="text-align: center"><td class="cst_new"></td><td class="cst_opened"></td><td class="cst_inprogress"></td><td class="cst_holded"></td></tr>
		</tbody></table></div></div></div>
		<div class="col col-md-7" style="padding-left: 0px;"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Ticket Summary</div><select class="form-control pull-right" name="period" onchange="PopulateTicketSummary(this.value)" style="width: 100px; margin-top: -28px; padding: 0px;">{!! GetTicketSummaryPeriodOptions() !!}</select></div><div class="panel-body"><table class="table table-bordered"><thead><tr><th>Total</th><th>Closed</th><th>New/Opened</th><th>Inprogress</th><th>Holded</th></tr></thead><tbody>
		<tr style="text-align: center"><td class="ts_total"></td><td class="ts_closed"></td><td class="ts_new"></td><td class="ts_inprogress"></td><td class="ts_hold"></td></tr>
		</tbody></table></div></div></div>
	</div>
	<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Active Tickets</div></div><div class="panel-body">
	@component('tkt.comp_tickets_st',['Tickets' => $Team->Tickets->filter(function($Item){ return !in_array($Item->Cstatus->status,['CLOSED','COMPLETED','RECREATED','DISMISSED']); })]) @endcomponent
	</div></div>
	<div class="row">
		<div class="col col-md-6"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Support Agents</div></div><div class="panel-body">
		@component('tsa.comp_agents',['Agents' => $Team->Agents]) @endcomponent
		</div></div></div>
		<div class="col col-md-6" style="padding-left: 0px;"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Distributors</div></div><div class="panel-body">
		@component('distributor.comp_distributors_st',['Distributors' => $Team->Distributors]) @endcomponent
		</div></div></div>
	</div>
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
function GetTicketSummaryPeriodOptions(){
	$opts = [1 => 'Total',strtotime(date('Y-m-d 00:00:00')) => 'Today', strtotime(date('Y-m-d 00:00:00',strtotime('-1 day'))) => 'From yesterday', strtotime(date('Y-m-d 00:00:00',strtotime('-2 day'))) => 'Last 2 Days', strtotime(date('Y-m-d 00:00:00',strtotime('-6 day'))) => '1 Week', strtotime(date('Y-m-d 00:00:00',strtotime('-29 days'))) => '1 Month', strtotime(date('Y-m-d 00:00:00',strtotime('-179 days'))) => '6 Months', strtotime(date('Y-m-d 00:00:00',strtotime('-364 days'))) => 'Year'];
	$optsArray = [];
	array_walk($opts,function($itm, $key)use(&$optsArray){
		$optsArray[] = '<option value="'.($key).'">'.$itm.'</option>';
	});
	return implode('',$optsArray);
}
function GetStatusReport($Tkt){
	$Status = []; if($Tkt->isEmpty()) return [];
	$Tkt->each(function($item, $key)use(&$Status){
		if(!array_key_exists($item->Cstatus->status,$Status)) $Status[$item->Cstatus->status] = [];
		if(!array_key_exists($item->code,$Status[$item->Cstatus->status])) $Status[$item->Cstatus->status][$item->code] = strtotime($item->created_at);
	});
	return $Status;
}
@endphp
@push('js')
<script type="text/javascript">
var _Status = {!! json_encode($StatusReport) !!};
function PopulateCurrentStatus(){
	$.each({'NEW':'new','OPENED':'opened','INPROGRESS':'inprogress','HOLD':'holded'},function(S,C){ $('td.cst_'+C).html(TicketListAnchor(_Status[S]?Object.keys(_Status[S]).length:0,S,null)); })
}
function PopulateTicketSummary(T){
	$('td[class^="ts_"]').text(0); TDTtl = $('td.ts_total').text(0);
	$.each({'NEW':'new','OPENED':'new','INPROGRESS':'inprogress','CLOSED':'closed','COMPLETED':'closed','HOLD':'hold','REOPENED':'closed','RECREATED':'closed'},function(S,C){
		TD = $('td.ts_'+C); TDTtl = $('td.ts_total');
		Ttl = (typeof _Status[S] == 'undefined') ? 0 : Object.values(_Status[S]).filter(t => parseInt(t) >= parseInt(T)).length
		TDT = TD.text()?parseInt(TD.text()):0; TD.html(TicketListAnchor(TDT+=Ttl,C,T));
		NT = TDTtl.text()?parseInt(TDTtl.text()):0; TDTtl.text(NT+=Ttl);
	})
}
function TicketListAnchor(C,S,P){
	A = $('<a>').attr({ target:'_blank', href:TicketListUrl(S,P) }).text(C).css({ color:'inherit' })
	return A[0];
}
function TicketListUrl(S,P){
	Url = '{{ Route("mit.tickets.list",[Request()->code,"--status--"]) }}'.replace("--status--",S);
	return (P || P == 0) ? Url+"?period="+P : Url;
}
$(function(){
	PopulateCurrentStatus()
	PopulateTicketSummary(1)
})
</script>
@endpush