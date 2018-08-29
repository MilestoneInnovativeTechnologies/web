@extends("tkt.page")
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><div class="panel-title">Ticket Lists  @if(isset($Title) && $Title) - <small>{{ $Title }}</small>@endif </div></div>
		<div class="panel-body">
			<div class="clearfix pagination">
				<div class="pull-right">{{ $Data->appends(['period' => Request()->period])->links() }}</div>
			</div>
			<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Ticket Details</th><th>Customer &amp; Product</th><th>Created</th><th>Status</th></tr></thead><tbody>
			@forelse($Data as $tkt)
			<tr>
				<td>{{ $loop->iteration }}</td>
				<td><a href="{{ Route('ticket.panel',$tkt->code) }}" target="_blank" style="color: inherit">{{ $tkt->code }}</a><br><a href="{{ Route('ticket.panel',$tkt->code) }}" style="color: inherit">{{ $tkt->title }}</a><br><small>{!! nl2br($tkt->description) !!}</small></td>
				<td><a href="{{ Route('customer.panel',$tkt->customer) }}" target="_blank" style="color: inherit">{{ $tkt->Customer->name }}</a><br><small>{{ $tkt->Product->name }} {{ $tkt->Edition->name }} Edition</small></td>
				<td>By: {{ $tkt->CreatedBy->name }}<br>On: {{ date('d/M/y h:i a',strtotime($tkt->created_at)) }}</td>
				<td><strong>{{ $tkt->Cstatus->status }}</strong>@if($tkt->Cstatus->status == 'HOLD' && $tkt->Cstatus->status_text)<br><small>({!! nl2br($tkt->Cstatus->status_text) !!})</small>@endif<br>On: {{ date('d/M h:i a',$tkt->Cstatus->start_time) }}<br><small>({{ Sec2Ago(time()-$tkt->Cstatus->start_time) }})</small></td>
			</tr>
			@empty
			<tr><th style="text-align: center" colspan="5">No records found!</th></tr>
			@endforelse
		</tbody></table></div>
		</div>
	</div>
</div>

@endsection
@php
function GetCategoryBreadCrumb($Obj){
	$BCAry = [];
	if($Obj) $BCAry[] = $Obj->name;
	if($Obj->parent) array_unshift($BCAry,GetCategoryBreadCrumb($Obj->Parent));
	return implode(" &raquo; ", $BCAry);
}
function Sec2Ago($s){
	if(60 > $s) return $s . ' secs'; $d = 60;
	if(3600 > $s) { $min = floor($s/$d); $sec = $s%$d; return join(" ",[$min,'mins',$sec,'secs']); } $d = 3600;
	if(86400 > $s) { $hrs = floor($s/$d); $min = floor(($s%$d)/60); return join(" ",[$hrs,'hrs',$min,'mins']); } $d = 86400;
	if(2592000 > $s) { $dys = floor($s/$d); $hrs = floor(($s%$d)/3600); return join(" ",[$dys,'days',$hrs,'hrs']); }
	$d = 2592000;	$mn = floor($s/$d); $dys = floor(($s%$d)/86400); return join(" ",[$mn,'months',$dys,'days']);
}
@endphp