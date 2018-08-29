<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>Seq No</th><th>Details</th><th>Created</th><th>Assigned</th><th>Status</th></tr></thead><tbody>
	@if($Tasks->isEmpty() || $Tasks->count() == 1)
	<tr><th colspan="5" style="text-align: center">No records found!</th></tr>
	@else
	@php $Tsks = $Tasks->load('Stype','CreatedBy','Responder.Assigner') @endphp
	@foreach($Tsks as $t) @continue($t->id == $Skip)
	<tr><th>{{ $t->seqno }}</th><td><a href="{{ Route('task.panel',$t->id) }}" style="color: inherit">{{ $t->title }}</a> @if($t->support_type) <em><small>({{ $t->Stype->name }})</small></em> @endif @if($t->description)<br><small>{!! nl2br($t->description) !!}</small>@endif</td><td>By: {{ $t->CreatedBy->name }}<br>On: <small>{{ date('d/M/y h:i a',strtotime($t->created_at)) }}</small></td><td>@if($t->Responder)To: <strong>{{ $t->Responder->Responder->name }}</strong><br>By: {{ $t->Responder->Assigner->name }}<br>On: {{ date('d/M/y h:i a',strtotime($t->Responder->created_at)) }}@endif</td><td>{{ $t->Cstatus->status }}@if($t->Cstatus->status_text)<br><small>{!! nl2br($t->Cstatus->status_text) !!}</small>@endif<br><small>({{ SecondDiff(time()-($t->Cstatus->start_time)) }})</small></td></tr>
	@endforeach
	@endif
</tbody></table></div>
@php
function SecondDiff($s){
	if(60 > $s) return $s . ' secs'; $d = 60;
	if(3600 > $s) { $min = floor($s/$d); $sec = $s%$d; return join(" ",[$min,'mins',$sec,'secs']); } $d = 3600;
	if(86400 > $s) { $hrs = floor($s/$d); $min = floor(($s%$d)/60); return join(" ",[$hrs,'hrs',$min,'mins']); } $d = 86400;
	if(2592000 > $s) { $dys = floor($s/$d); $hrs = floor(($s%$d)/3600); return join(" ",[$dys,'days',$hrs,'hrs']); }
	$d = 2592000;	$mn = floor($s/$d); $dys = floor(($s%$d)/86400); return join(" ",[$mn,'months',$dys,'days']);
}
@endphp