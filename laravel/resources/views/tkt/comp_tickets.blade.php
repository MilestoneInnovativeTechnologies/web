<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Ticket</th><th>Title &amp; Description</th><th>Product</th><th>Current Status</th><th>Timings</th></tr></thead><tbody>
	@if($Tickets && $Tickets->isNotEmpty()) @foreach($Tickets as $tkt)
	<tr><th>{{ $loop->iteration }}</th><td><a href="{{ Route('ticket.panel',$tkt->code) }}" style="text-decoration: none; color: inherit">{{ $tkt->code }}</a></td>
	<td><a href="{{ Route('ticket.panel',$tkt->code) }}" style="text-decoration: none; color: inherit">{{ $tkt->title }}</a>@if($tkt->description)<br><small>({!! nl2br($tkt->description) !!})</small>@endif</td>
	<td>{{ GetProductFromTicket($tkt) }}</td>
		@php if(!$tkt->Cstatus) dd($tkt->toArray()); @endphp
	<td>{{ $tkt->Cstatus->status }}@if($tkt->Cstatus->status == 'HOLD' && $tkt->Cstatus->status_text)<br><small>({!! nl2br($tkt->Cstatus->status_text) !!})</small>@endif</td>
	<td nowrap>{!! GetTicketTimings($tkt->Status) !!}</td></tr>
	@endforeach @else
	<tr><th colspan="6" style="text-align: center">No records found!</th></tr>
	@endif
</tbody></table></div>	
@php
function GetProductFromTicket($tkt){
	return implode(' ',[$tkt->Product->name,$tkt->Edition->name,'Edition']);
}
function GetTicketTimings($Status){
	$Start = 0; $TimeToOpen = 0; $OpenToWork = 0; $Worked = 0; $Holded = 0; $StartToClose = 0; $Closed = 0;
	foreach($Status as $State){
		if($Start == 0 && $State->status == 'NEW'){ $Start = $State->start_time; continue; }
		if($Start && $State->status == 'OPENED'){ $TimeToOpen = $State->start_time - $Start; continue; }
		if($TimeToOpen && $State->status == 'INPROGRESS'){
			if($OpenToWork == 0) $OpenToWork = $State->start_time - $Start - $TimeToOpen;
			$Worked += $State->total;
			continue;
		}
		if($OpenToWork && $State->status == 'HOLD') { $Holded += $State->total; continue; }
		if($Start && $OpenToWork && $Worked && $State->status == 'CLOSED') { $StartToClose = $State->start_time - $Start; $Closed = $State->start_time; continue; }
	}
	$line = [];
	$line[] = 'Created On: '.date('d/M/y',$Start); $line[] = 'Delay to Open: '.SecDiff($TimeToOpen); $line[] = 'Open to Work: '.SecDiff($OpenToWork);
	$line[] = 'Total Worked: '.SecDiff($Worked); $line[] = 'Total Holded: '.SecDiff($Holded);
	if($Closed) $line[] = 'Closed On: ' . date('d/M/y',$Closed);
	if($Closed) $line[] = 'Total Time: ' . SecDiff($StartToClose);
	return implode('<br>',$line);
}
function SecDiff($s){
	if(60 > $s) return $s . ' secs'; $d = 60;
	if(3600 > $s) { $min = floor($s/$d); $sec = $s%$d; return join(" ",[$min,'mins',$sec,'secs']); } $d = 3600;
	if(86400 > $s) { $hrs = floor($s/$d); $min = floor(($s%$d)/60); return join(" ",[$hrs,'hrs',$min,'mins']); } $d = 86400;
	if(2592000 > $s) { $dys = floor($s/$d); $hrs = floor(($s%$d)/3600); return join(" ",[$dys,'days',$hrs,'hrs']); }
	$d = 2592000;	$mn = floor($s/$d); $dys = floor(($s%$d)/86400); return join(" ",[$mn,'months',$dys,'days']);
}
@endphp