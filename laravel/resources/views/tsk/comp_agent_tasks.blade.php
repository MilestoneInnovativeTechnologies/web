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
<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Task</th><th>Customer &amp; Ticket</th><th>Action</th></tr></thead><tbody>
	@forelse($Tasks as $t)
	<tr>
		<th>{{ $loop->iteration }}</th>
		<td><a href="{{ Route('task.panel',$t->id) }}" style="color: inherit">{{ $t->seqno }}. {{ $t->title }}<br><small>{!! nl2br($t->description) !!}</small></a></td>
		<td><a href="{{ Route('customer.panel',$t->Ticket->customer) }}" style="color: inherit">{{ $t->Ticket->Customer->name }}</a><br>Ticket: <small><a href="{{ Route('ticket.panel',$t->ticket) }}" style="color: inherit">{{ $t->Ticket->title }}</a></small></td>
		<td>{!! GetTaskActions($t) !!}</td>
	</tr>
	@empty
	<tr><th colspan="6" style="text-align: center">No records found!</th></tr>
	@endforelse
</tbody></table></div>