<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Status</th><th>User &amp; Time</th><th>Total Time</th></tr></thead><tbody>
	@forelse($Status as $s)
	<tr><th>{{ $loop->iteration }}</th><td>{{ $s->status }}@if(in_array($s->status,['HOLD'])) <br><small>{!! nl2br($s->status_text) !!}</small> @endif</td><td nowrap style="font-size: 12px;">{{ $s->User->name }}<br>{{ date('d/m/y h:i a',$s->start_time) }}</td><td nowrap style="font-size: 13px;">@if($s->total) {{ SecDifference($s->total) }} @endif</td></tr>
	@empty
	<tr><th colspan="4" style="text-align: center">No records found!</th></tr>
	@endforelse
</tbody></table></div>
@php
function SecDifference($s){
	if(60 > $s) return $s . ' secs'; $d = 60;
	if(3600 > $s) { $min = floor($s/$d); $sec = $s%$d; return join(" ",[$min,'mins',$sec,'secs']); } $d = 3600;
	if(86400 > $s) { $hrs = floor($s/$d); $min = floor(($s%$d)/60); return join(" ",[$hrs,'hrs',$min,'mins']); } $d = 86400;
	if(2592000 > $s) { $dys = floor($s/$d); $hrs = floor(($s%$d)/3600); return join(" ",[$dys,'days',$hrs,'hrs']); }
	$d = 2592000;	$mn = floor($s/$d); $dys = floor(($s%$d)/86400); return join(" ",[$mn,'months',$dys,'days']);
}
@endphp