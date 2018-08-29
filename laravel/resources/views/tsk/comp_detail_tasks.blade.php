@php
if(!function_exists('GetTaskActions')){
	function GetTaskActions($tsk){
		$Actions = $tsk->available_action;
		$TitleIcon = ['view'=>['View task details','list-alt'],'edit'=>['Edit task details','edit'],'delete'=>['Delete this task','remove'],'chngrsp'=>['Assign/Change responder','user'],'open'=>['Open this task','open-file'],'recheck'=>['Submit for rechecking the task','refresh'],'work'=>['Start/Continue handling this task','forward'],'hold'=>['Hold this task','pause'],'close'=>['Close this task','ok']];
		$Id = $tsk->id;
		return implode("",array_map(function($action)use($Id,$TitleIcon){
			return glyLink(Route('tsk.'.$action,['tsk'=>$Id]), $TitleIcon[$action][0], $TitleIcon[$action][1], ['class'=>'btn btn-none']);
		},$Actions));
	}
}
@endphp
<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Task</th><th>Task Details</th><th>Customer &amp; Ticket</th><th>Ticket Details</th><th style="width: 100px">Actions</th></tr></thead><tbody>
	@forelse($Tasks as $t)
	<tr>
		<th>{{ $loop->iteration }}</th>
		<td><a href="{{ Route('task.panel',$t->id) }}" style="color: inherit">{{ $t->seqno }}. {{ $t->title }}</a><br>{!! nl2br($t->description) !!}</td>
		<td><strong>Created</strong><br><em>By</em>: <small>{{ $t->CreatedBy->name }}</small><br><em>On</em>: <small>{{ date('d/M h:i a',strtotime($t->created_at)) }}</small><br><strong>Assigned</strong><br><em>To</em>: <small>{{ ($t->Responder)?$t->Responder->Responder->name:'' }}</small><br><em>By</em>: <small>{{ ($t->Responder)?$t->Responder->Assigner->name:'' }}</small><br><em>On</em>: <small>{{ ($t->Responder)?(date('d/M h:i a',strtotime($t->Responder->created_at))):'' }}</small></td>
		<td><a href="{{ Route('customer.panel',$t->Ticket->customer) }}" style="color: inherit">{{ $t->Ticket->Customer->name }}</a><br><small>Ticket: <a href="{{ Route('ticket.panel',$t->ticket) }}" style="color: inherit">{{ $t->Ticket->title }}</a></small><br><small>{!! nl2br($t->Ticket->description) !!}</small></td>
		<td>Created <em>By</em>: <small>{{ $t->Ticket->CreatedBy->name }}</small><br>On: <small>{{ date('d/M h:i a',strtotime($t->Ticket->created_at)) }}</small><br>Status: <small>{{ $t->Ticket->Cstatus->status }}</small></td>
		<td>{!! GetTaskActions($t) !!}</td></tr>
	@empty
	<tr><th colspan="6" style="text-align: center">No records found!</th></tr>
	@endforelse
</tbody></table></div>
