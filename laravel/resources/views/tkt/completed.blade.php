@extends("tkt.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Support Tickets</strong></div>
		<div class="panel-body">
			<div class="clearfix pagination">
				<div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search for tickets" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchText()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
				<div class="pull-right">{{ $Links }}</div>
			</div>@if($Data->isNotEmpty())
			<div class="table-responsive tickets_list">
				<table class="table table-bordered tickets">
					<thead><tr><th>Code</th><th>Details</th><th>Customer &amp; Product</th><th>Timings</th>@unless(session()->get('_rolename') == 'customer')<th>Closure Done</th>@endunless<th>Action</th></tr></thead>
					<tbody>@foreach($Data as $Obj)
						<tr>
							<td><strong>{{ $Obj->code }}</strong><br><small>({{ $Obj->priority }})</small></td>
							<td><strong>{{ $Obj->title }}</strong>{!! ($Obj->Category)?' <small>('.$Obj->Category->name.')</small>':'' !!}<br>{{ $Obj->description }}</td>
							<td><strong>{{ $Obj->Customer->name }}</strong><br>+{{ $Obj->Customer->Details->phonecode }}-{{ $Obj->Customer->Details->phone }}<br>{{ $Obj->Customer->Logins->implode('email',',') }}<br><strong>{{ $Obj->Product->name }} {{ $Obj->Edition->name }} Edition</strong></td>
							<td>{!! GetTicketTimings($Obj->Status) !!}</td>
							@unless(session()->get('_rolename') == 'customer')<td style="text-align: center">{{ ($Obj->Closure)?'YES':'NO' }}</td>@endunless
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
	$TitleIcon = ['view' => ['View ticket in detail','list-alt'], 'edit' => ['Edit this ticket','edit'], 'delete' => ['Delete this ticket','remove'], 'entitle' => ['Entitle this ticket','pencil'],'reassign' => ['Assign ticket to another Support Team','user'],'tasks' => ['Manage Tasks','tasks'], 'communicate' => ['Chat with Support Team','comment'],'reopen' => ['Reopen this Ticket','repeat'],'closure' => ['Proceed Ticket closure activities','paperclip'], 'complete' => ['Complete this ticket','ok'],'feedback' => ['Provide feedback about this ticket','check'],'recreate' => ['Recreate same ticket','duplicate'],'enquire' => ['Enquire with customer','headphones'],'close' => ['Close ticket','eye-close'],'transcript' => ['View chat transcript','italic'],'dismiss' => ['Dismiss ticket with reason','trash']];
	return implode('',array_map(function($act)use($Obj,$TitleIcon){ 
	 return glyLink(Route('tkt.'.$act,$Obj->code), $TitleIcon[$act][0], $TitleIcon[$act][1], ['class' => 'btn btn-link']);
	},$avps));
}
function GetTicketTimings($Status){
	$Start = 0; $TimeToOpen = 0; $OpenToWork = 0; $Worked = 0; $Holded = 0; $StartToClose = 0;
	foreach($Status as $State){
		if($Start == 0 && $State->status == 'NEW'){ $Start = $State->start_time; continue; }
		if($Start && $State->status == 'OPENED'){ $TimeToOpen = $State->start_time - $Start; continue; }
		if($TimeToOpen && $State->status == 'INPROGRESS'){
			if($OpenToWork == 0) $OpenToWork = $State->start_time - $Start - $TimeToOpen;
			$Worked += $State->total;
			continue;
		}
		if($OpenToWork && $State->status == 'HOLD') { $Holded += $State->total; continue; }
		if($Start && $OpenToWork && $Worked && $State->status == 'CLOSED') { $StartToClose = $State->start_time - $Start; continue; }
	}
	return implode('<br>',['Delay to Open: '.SecDiff($TimeToOpen),'Opened to Working: '.SecDiff($OpenToWork),'Total Worked: '.SecDiff($Worked),'Total Holded: '.SecDiff($Holded),'Total Time: '.SecDiff($StartToClose)]);
}
function SecDiff($s){
	if(60 > $s) return $s . ' secs'; $d = 60;
	if(3600 > $s) { $min = floor($s/$d); $sec = $s%$d; return join(" ",[$min,'mins',$sec,'secs']); } $d = 3600;
	if(86400 > $s) { $hrs = floor($s/$d); $min = floor(($s%$d)/60); return join(" ",[$hrs,'hrs',$min,'mins']); } $d = 86400;
	if(2592000 > $s) { $dys = floor($s/$d); $hrs = floor(($s%$d)/3600); return join(" ",[$dys,'days',$hrs,'hrs']); }
	$d = 2592000;	$mn = floor($s/$d); $dys = floor(($s%$d)/86400); return join(" ",[$mn,'months',$dys,'days']);
}
@endphp