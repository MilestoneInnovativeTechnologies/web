@php
if(!function_exists('SReqActions')){
	function SReqActions($Obj,$Prop,$Pref = 'sreq',$PK = 'id',$Title = 'action_title',$Icon = 'action_icon'){
		$lacs = $Obj->_GETARRAYVALUES($Obj->actions,$Obj->list_actions);
		$LI = []; foreach($Obj->$Prop as $act){
			if(!in_array($act,$lacs)) continue;
			$LI[] = glyLink(Route($Pref.'.'.$act,[$Obj->$PK]),$Obj->$Title[$act],$Obj->$Icon[$act],['class' => 'btn', 'attr' => 'style="padding:6px 4px;"']);
		}
		return implode(' ',$LI);
	}
}
@endphp
<div class="table-responsive">
	<table class="table table-bordered">
		<thead><tr>
			<th>No</th>
			<th>Message</th>
			<th>User</th>
			<th>Action</th>
		</tr></thead><tbody>
			@forelse($sregs as $k => $sreg)
			<tr>
				<td>{{ $loop->iteration }}</td>
				<td>{{ $sreg->message }}</td>
				<td>{{ $sreg->User->name }}</td>
				<td>{!! SReqActions($sreg,'available_actions') !!}</td>
			</tr>
			@empty
			<tr><th colspan="4" class="text-center">no records found!</th></tr>
			@endforelse
		</tbody>
	</table>
</div>
