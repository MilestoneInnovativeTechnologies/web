@extends("tsk.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Task - {{ $Task->id }}</strong> ({{ $Task->ticket }}){!! PanelHeadBackButton((url()->previous() == url()->current())?(Route('tsk.index')):(url()->previous())) !!}</div>
		<div class="panel-body clearfix">
		
			<div class="row">
				<div class="col-md-3">
					<a href="{{ Route('tsk.work',['tsk'	=>	$Task->id]) }}" title="Start working on the task" class="btn btn-none btn-primary jumbotron" style="color:#FFF; width: 100%; padding-right:0px; padding-left:0px; margin-bottom: 5px">Start Working</a>
					<a href="{{ Route('tsk.recheck',['tsk'	=>	$Task->id]) }}" title="Submit for Recheck" class="btn btn-none btn-info jumbotron" style="color:#FFF; width: 100%; padding-right:0px; padding-left:0px;">Submit to rechecking the task</a>
				</div>
				<div class="col-md-5">
					<div class="panel panel-default task_basic">
						<div class="panel-heading">Task</div>
						<div class="panel-body">
							<div class="table table-responsive"><table class="table table-striped"><tbody>
								<tr><th>Task ID</th><td>{{ $Task->id }}</td><th>Support Type</th><td>{{ $Task->suppoty_type?$Task->Stype->name:'none' }}</td></tr>
								<tr><th>Sequence</th><td colspan="3"><strong>{{ $Task->seqno }}</strong></td></tr>
								<tr><th>Title</th><td colspan="3">{{ $Task->title }}</td></tr>
								<tr><th>Description</th><td colspan="3">{{ $Task->description }}</td></tr>
							</tbody></table></div>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="panel panel-default task_details">
						<div class="panel-heading">Task Details</div>
						<div class="panel-body">
							<div class="table table-responsive"><table class="table table-striped"><tbody>
								<tr><th>Task Created By</th><td>{{ $Task->Createdby->name }}<th>On</th><td class="rdt_t">{{ $Task->created_at }}</td></tr>
								<tr><th>Assigned By</th><td>{{ ($Task->Responder)?$Task->Responder->Responder->name:'' }}<th>On</th><td class="rdt_t">{{ ($Task->Responder)?$Task->Responder->created_at:'' }}</td></tr>
								<tr><th>Current Status</th><td><strong>{{ $Task->Cstatus->status }}</strong><th>On</th><td class="rdt_t">{{ $Task->Cstatus->created_at }}</td></tr>
							</tbody></table></div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default task_statuses">
						<div class="panel-heading">Task Status flows</div>@php $Sts = $Task->Status @endphp
						<div class="panel-body">
							<div class="table table-responsive">
								<table class="table table-striped">
									<thead><tr><th>No</th><th>Date</th><th>Time</th><th>Status</th><th>End Time</th><th>Total Time spend</th><th>User</th></tr></thead>
									<tbody>@foreach($Sts as $St)
										<tr><td>{{ $loop->iteration }}</td><td>{{ date('D d/m',$St->start_time) }}</td><td>{{ date("H:i:s",$St->start_time) }}</td><td>{{ $St->status }} @if($St->status_text)<br><small>{!! nl2br($St->status_text) !!}</small> @endif</td>
											<td>@if($St->end_time) {{ date('D d/m, H:i:s',$St->end_time) }} @endif</td>
											<td>@if($St->end_time) {{ SecDiff($St->total) }} @endif</td>
											<td>{{ $St->User->name }}</td>
										</tr>
									@endforeach</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default ticket_basic">
						<div class="panel-heading">Ticket Details</div>@php $Tkt = $Task->Ticket @endphp
						<div class="panel-body">
							<div class="table table-responsive"><table class="table table-striped"><tbody>
								<tr><th>Code</th><td>{{ $Tkt->code }}</td><th>Type</th><td>{{ $Tkt->ticket_type?$Tkt->Type->name:'none' }}</td></tr>
								<tr><th>Title</th><td>{{ $Tkt->title }}</td><th>Ticket category</th><td>{{ ($Tkt->category)?GetCategoryBreadCrumb($Tkt->Category):'' }}</td></tr>
								<tr><th>Description</th><td>{{ $Tkt->description }}</td><th>Priority</th><td>{{ $Tkt->priority }}</td></tr>
								<tr><th>Customer</th><td>{{ $Tkt->Customer->name }}</td><th>Product</th><td>{{ $Tkt->Product->name }} {{ $Tkt->Edition->name }} Edition</td></tr>
								<tr><th>Created By</th><td>{{ $Tkt->Createdby->name }}<th>On</th><td class="rdt_t">{{ $Tkt->created_at }}</td></tr>
								<tr><th>Status</th><td><strong>{{ $Tkt->Cstatus->status }}</strong><th>On</th><td class="rdt_t">{{ $Tkt->Cstatus->created_at }}</td></tr>
							</tbody></table></div>
						</div>
					</div>
				</div>
			</div>

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
function GetActions($tsk){
	$Actions = array_reverse($tsk->available_action); $TtlActions = count($Actions);
	$TitleIcon = ['view'=>['View task details','list-alt'],'edit'=>['Edit task details','edit'],'delete'=>['Delete this task','remove'],'chngrsp'=>['Assign/Change responder','user'],'open'=>['Open this task','open-file'],'recheck'=>['Submit for rechecking the task','refresh'],'work'=>['Start handling this task','transfer'],'hold'=>['Hold this ticket','stop'],'close'=>['Close this task','ok']];
	$Id = $tsk->id;
	return implode("",array_map(function($action)use($Id,$TitleIcon,$TtlActions){
		return '<a href="'.Route('tsk.'.$action,['tsk'=>$Id]).'" title="'.$TitleIcon[$action][0].'" class="btn btn-none btn-primary" style="margin: 0px 0px 1px 0px; width:100%">'.$TitleIcon[$action][0].'</a>';
	},$Actions));
}
function SecDiff($s){
	if(60 > $s) return $s . ' secs'; $d = 60;
	if(3600 > $s) { $min = floor($s/$d); $sec = $s%$d; return join(" ",[$min,'mins',$sec,'secs']); } $d = 3600;
	if(86400 > $s) { $hrs = floor($s/$d); $min = floor(($s%$d)/60); return join(" ",[$hrs,'hrs',$min,'mins']); } $d = 86400;
	if(2592000 > $s) { $dys = floor($s/$d); $hrs = floor(($s%$d)/3600); return join(" ",[$dys,'days',$hrs,'hrs']); }
	$d = 2592000;	$mn = floor($s/$d); $dys = floor(($s%$d)/86400); return join(" ",[$mn,'months',$dys,'days']);
}
@endphp
@push('js')
<script type="text/javascript">
$(function(){
	$('.rdt_t').each(function(i,col){ $(col).html(UserfriendlyDate($(col).text(),true)) })
	$('.rdt').each(function(i,col){ $(col).html(UserfriendlyDate($(col).text(),false)) })
})
function UserfriendlyDate(txt,ago){
	ago = ago || false;
	Ary = [ReadableDate(txt)];
	if(ago){
		d = DateDiff(txt,'d'); h = DateDiff(txt,'h'); m = DateDiff(txt,'m'); s = DateDiff(txt,'s');
		ago_text = [];
		if(d){ ago_text[0] = d; ago_text[1] = 'days'; ago_text[2] = h%24; ago_text[3] = 'hrs'; }
		else if(h) { ago_text[0] = h; ago_text[1] = 'hrs'; ago_text[2] = m%60; ago_text[3] = 'mins'; }
		else if(m) { ago_text[0] = m; ago_text[1] = 'mins'; ago_text[2] = s%60; ago_text[3] = 'secs'; }
		else { ago_text[0] = s; ago_text[1] = 'secs'; }
		Ary.push($('<br>'))
		Ary.push($('<small>').text(ago_text.join(" ")))
		Ary.push(' ago')
	}
	return Ary;
}
</script>
@endpush