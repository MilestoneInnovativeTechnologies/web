@extends("tsk.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Ticket Tasks</strong></div>
		<div class="panel-body">
			<div class="clearfix pagination">
				<div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search for tickets" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchText()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
				<div class="pull-right">{{ $Links }}</div>
			</div>@if($Data->isNotEmpty())
			<div class="table-responsive">
				<table class="table table-bordered">
					<thead><tr><th>Ticket/Customer</th><th>Task Title</th>@if(session()->get('_rolename') == 'supportagent')<th>Support Type</th>@else<th>Responder</th>@endif<th>Active</th><th>Status</th><th>Created On</th><th>Action</th></tr></thead>
					<tbody>@foreach($Data as $tkt => $Array)
						<tr>
							<td rowspan="{{ $Array->count() }}" style="vertical-align: middle"><strong>{{ $tkt }}</strong><br>{{ $Array[0]->Ticket->Customer->name }}</td>
							@foreach($Array as $Obj)
							<td><strong>{{ $Obj->seqno }}.</strong> {{ $Obj->title }}</td>
							@if(session()->get('_rolename') == 'supportagent')<td>{{ ($Obj->support_type)?$Obj->Stype->name:'' }}</td>
							@else<td>{{ ($Obj->Responder)?$Obj->Responder->Responder->name:'' }}</td>
							@endif<td>{!! ($Obj->status == 'INACTIVE')?'INACTIVE<br><small>Waiting for some tasks to complete</small>':'ACTIVE' !!}</td>
							<td>{{ $Obj->Cstatus->status }}@if($Obj->Cstatus->status_text) <br><small>({!! nl2br($Obj->Cstatus->status_text) !!})</small> @endif</td>
							<td><script>d = DateDiff('{{ $Obj->created_at }}','d'); h = DateDiff('{{ $Obj->created_at }}','h'); m = DateDiff('{{ $Obj->created_at }}','m'); document.write(ReadableDate('{{ $Obj->created_at }}'));document.write("<br><small>");document.write((d)?(d+' days '+(parseInt(d%h))+' hrs'):((h)?(h+' hrs '+(parseInt(h%m))+' mins'):((m)?(m+' mins'):'0 mins')));document.write(" ago");document.write("</small>");</script></td>
							<td>{!! GetActions($Obj) !!}</td>
							@if($loop->remaining) </tr><tr> @endif
							@endforeach
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
function GetActions($tsk){
	$Actions = $tsk->available_action;
	$TitleIcon = ['view'=>['View task details','list-alt'],'edit'=>['Edit task details','edit'],'delete'=>['Delete this task','remove'],'chngrsp'=>['Assign/Change responder','user'],'open'=>['Open this task','open-file'],'recheck'=>['Submit for rechecking the task','refresh'],'work'=>['Start/Continue handling this task','forward'],'hold'=>['Hold this task','pause'],'close'=>['Close this task','ok']];
	$Id = $tsk->id;
	return implode("",array_map(function($action)use($Id,$TitleIcon){
		return glyLink(Route('tsk.'.$action,['tsk'=>$Id]), $TitleIcon[$action][0], $TitleIcon[$action][1], ['class'=>'btn btn-none']);
	},$Actions));
}
@endphp