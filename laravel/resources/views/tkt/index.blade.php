@extends("tkt.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Support Tickets</strong>{!! PanelHeadAddButton(Route('tkt.create'),'Create New Support Ticket') !!}</div>
		<div class="panel-body">
			<div class="clearfix pagination">
				<div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search for tickets" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchText()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
				<div class="pull-right">{{ $Links }}</div>
			</div>@if($Data->isNotEmpty())
			<div class="table-responsive tickets_list">
				<table class="table table-bordered tickets">
					<thead><tr><th>Code</th><th>Details</th><th>Customer &amp; Product</th><th>Status</th><th>Action</th></tr></thead>
					<tbody>@foreach($Data as $Obj)
						<tr cstatus="{{ $Obj->Cstatus->status }}" data-code="{{ $Obj->code }}">
							<td><strong>{{ $Obj->code }}</strong>@unless(session()->get('_rolename') == 'customer')<br><small>({{ $Obj->priority }})</small>@endunless</td>
							<td><strong>{{ $Obj->title }}</strong>{!! ($Obj->Category)?' <small>('.$Obj->Category->name.')</small>':'' !!}<br>{{ $Obj->description }}</td>
							<td><strong>{{ $Obj->Customer->name }}</strong><br>+{{ $Obj->Customer->Details->phonecode }}-{{ $Obj->Customer->Details->phone }}<br>{{ $Obj->Customer->Logins->implode('email',',') }}<br><strong>{{ $Obj->Product->name }} {{ $Obj->Edition->name }} Edition</strong></td>
							<td class="status">{{ $Obj->Cstatus->status }}@if($Obj->Cstatus->status == 'HOLD') <br><small>({{ $Obj->Cstatus->status_text }})</small> @endif
							@unless(in_array($Obj->Cstatus->status,['NEW']))<br><br><strong>Responders:</strong><br><ol><li>{!! $Obj->get_responders()->implode('</li><li>') !!}</li></ol>@endunless
							</td>
							<td nowrap>{!! GetActions($Obj) !!}</td>
						</tr>
					@endforeach</tbody>
				</table>
			</div>@else
			<div class="jumbotron">
				<h2 class="text-center">No Records found</h2>
			</div>@endif
		</div>
	</div>
</div>

@endsection
@php
function GetActions($Obj){
	$avps = $Obj->available_actions;
	if(empty($avps)) return '';
	$TitleIcon = ['view' => ['View ticket in detail','list-alt'], 'edit' => ['Edit this ticket','edit'], 'delete' => ['Delete this ticket','remove'], 'entitle' => ['Entitle this ticket','pencil'],'reassign' => ['Assign ticket to another Support Team','user'],'tasks' => ['Manage Tasks','tasks'], 'communicate' => ['Chat with Support Team','comment'],'reopen' => ['Reopen this Ticket','repeat'],'closure' => ['Proceed Ticket closure activities','paperclip'], 'complete' => ['Complete this ticket','ok'],'feedback' => ['Provide feedback about this ticket','check'],'recreate' => ['Recreate same ticket','duplicate'],'enquire' => ['Enquire with customer','headphones'],'close' => ['Close ticket','eye-close'],'req_complete' => ['Send a mail to customer requesting to complete this ticket','envelope'],'force_complete' => ['Forcibly make this ticket as completed','eject'],'transcript' => ['View chat transcript','italic'],'dismiss' => ['Dismiss ticket with reason','trash']];
	return implode('',array_map(function($act)use($Obj,$TitleIcon){ 
	 return glyLink(Route('tkt.'.$act,$Obj->code), $TitleIcon[$act][0], $TitleIcon[$act][1], ['class' => 'btn btn-link']);
	},$avps));
}
@endphp
@push('js')
<script type="text/javascript">
$(function(){
	@unless(session()->get('_rolename') == 'customer')
	$INPROGRESS = $('tr[cstatus="INPROGRESS"]');
	if($INPROGRESS.length){
		$INPROGRESS.each(function(i,TR){ code = $(TR).data('code'); GetTicketProgress(code); });
	}@endunless
});
function GetTicketProgress(code){
	FireAPI('api/v1/tkt/get/prs',function(a){
		$('[data-code="'+a.tkt+'"]').find('.status').append($('<progress>').attr({max:a.max,value:a.val}).css({display:'block',height:30}))
	},{tkt:code})
}
</script>
@endpush