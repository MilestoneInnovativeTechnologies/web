@extends("smsg.page")
@include('BladeFunctions')
@section("content")
@php
$code = Request()->code;
$Data = ($code) ? \App\Models\SMSGateway::find($code) : null;
@endphp

<div class="content"><form method="post">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Inactivate SMS Gateways, {{ $Data->name }}</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('smsg.index'):url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="well"> Are you sure, Do you want to make this gateway as <strong>INACTIVE</strong>?? </div>
		</div>
		<div class="panel-footer clearfix">
			<input type="submit" name="action" value="Inactivate Gateway" class="btn btn-primary pull-right">
		</div>
	</div></form>
</div>

@endsection
@php
function ActionsToListIcons($Obj,$Prop = 'available_actions',$Pref = 'smsg',$PK = 'code',$Title = 'action_title',$Icon = 'action_icon',$Modal = 'modal_actions'){
	$LI = [];
	foreach($Obj->$Prop as $act){
		if(in_array($act,$Obj->$Modal)) continue;
		$LI[] = glyLink(Route($Pref.'.'.$act,[$Obj->$PK]),$Obj->$Title[$act],$Obj->$Icon[$act],['class' => 'btn', 'attr' => 'style="padding:6px 6px;"']);
	}
	return implode('',$LI);
}
@endphp