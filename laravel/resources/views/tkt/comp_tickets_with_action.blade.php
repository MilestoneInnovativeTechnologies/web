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
<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Ticket Details</th><th>Category</th><th>Customer</th><th>Created</th><th style="width: 100px;">Action</th></tr></thead><tbody>
	@forelse($Tickets as $t)
	<tr>
		<th>{{ $loop->iteration }}</th>
		<td><a href="{{ Route('ticket.panel',$t->code) }}" style="color: inherit"><small>{{ $t->code }}</small></a><br><strong>{{ $t->title }}</strong><br><small>{{ $t->description }}</small></td>
		<td>@if($t->category) {{ $t->Category->name }} @endif @if($t->Category_specs && $t->Category_specs->isNotEmpty()) @php $t->Category_specs->each(function($spec){ echo '<br><small><strong>' . $spec->Specification->name . ': </strong>' . (($spec->value_text)?:$spec->Value->name) . '</small>'; }) @endphp @endif</td>
		<td><a href="{{ Route('customer.panel',$t->customer) }}" style="color: inherit">{{ $t->Customer->name }}</a><br><small>{{ $t->Product->name }} {{ $t->Edition->name }} Edition</small></td>
		<td>By: <small>{{ $t->CreatedBy->name }}</small><br>On: <small>{{ date('d/M h:i a',strtotime($t->created_at)) }}</small><br>Status: <small>{{ $t->Cstatus->status }}</small></td>
		<td>{!! GetTicketListActions($t) !!}</td>
	</tr>
	@empty
	<tr><th colspan="6" style="text-align: center">No records found!</th></tr>
	@endforelse
</tbody></table></div>
