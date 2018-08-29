@php
if(!function_exists('SecDiff')){
	function SecDiff($s){
		if(60 > $s) return $s . ' secs'; $d = 60;
		if(3600 > $s) { $min = floor($s/$d); $sec = $s%$d; return join(" ",[$min,'mins',$sec,'secs']); } $d = 3600;
		if(86400 > $s) { $hrs = floor($s/$d); $min = floor(($s%$d)/60); return join(" ",[$hrs,'hrs',$min,'mins']); } $d = 86400;
		if(2592000 > $s) { $dys = floor($s/$d); $hrs = floor(($s%$d)/3600); return join(" ",[$dys,'days',$hrs,'hrs']); }
		$d = 2592000;	$mn = floor($s/$d); $dys = floor(($s%$d)/86400); return join(" ",[$mn,'months',$dys,'days']);
	}
}
if(!function_exists('GetTicketListActions')){
	function GetTicketListActions($Obj){
		$avps = $Obj->available_actions;
		if(empty($avps)) return '';
		$TitleIcon = ['view' => ['View ticket in detail','list-alt'], 'edit' => ['Edit this ticket','edit'], 'delete' => ['Delete this ticket','remove'], 'entitle' => ['Entitle this ticket','pencil'],'reassign' => ['Assign ticket to another Support Team','user'],'tasks' => ['Manage Tasks','tasks'], 'communicate' => ['Chat with Support Team','comment'],'reopen' => ['Reopen this Ticket','repeat'],'closure' => ['Proceed Ticket closure activities','paperclip'], 'complete' => ['Complete this ticket','ok'],'feedback' => ['Provide feedback about this ticket','check'],'recreate' => ['Recreate same ticket','duplicate'],'enquire' => ['Enquire with customer','headphones'],'close' => ['Close ticket','eye-close'],'req_complete' => ['Send a mail to customer requesting to complete this ticket','envelope'],'force_complete' => ['Forcibly make this ticket as completed','eject'],'transcript' => ['View chat transcript','italic'],'dismiss' => ['Dismiss ticket with reason','trash']];
		return implode('',array_map(function($act)use($Obj,$TitleIcon){ 
		 return glyLink(Route('tkt.'.$act,$Obj->code), $TitleIcon[$act][0], $TitleIcon[$act][1], ['class' => 'btn btn-link']);
		},$avps));
	}
}
@endphp
<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Ticket</th><th>Created</th><th>Status</th><th style="width: 100px;">Action</th></tr></thead><tbody>
	@forelse($Tickets as $t)
	<tr>
		<th>{{ $loop->iteration }}</th>
		<td><small>{{ $t->code }}</small><br><strong>{{ $t->title }}</strong><br><small>{{ $t->description }}</small><br><small>Product: <em>{{ $t->Product->name }} {{ $t->Edition->name }} Edition</em></small></td>
		<td><small><strong>By:</strong> {{ $t->CreatedBy->name }}</small><br><small><strong>On:</strong> {{ date('d/M h:i a',strtotime($t->created_at)) }}</small><br><small>({{ SecDiff(time()-(strtotime($t->created_at))) }} ago)</small></td>
		<td><strong>{{ $t->Cstatus->status }}</strong><br><small>({{ SecDiff(time()-($t->Cstatus->start_time)) }} ago)</small>@if($t->Cstatus->status == 'HOLD' && $t->Cstatus->status_text) <br>{!! nl2br($t->Cstatus->status_text) !!} @endif</td>
		<td>{!! GetTicketListActions($t) !!}</td>
	</tr>
	@empty
	<tr><th colspan="5" style="text-align: center">No records found!</th></tr>
	@endforelse
</tbody></table></div>
