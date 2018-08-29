@extends("sreq.page")
@include('BladeFunctions')
@section("content")
@php $SReq = new \App\Models\ServiceRequest; $ORM = $SReq->query(); @endphp
@php if(Request()->search_text){ $st = '%'.Request()->search_text.'%'; $ORM = $ORM->where(function($Q)use($st){ $Q->whereHas('Supportteam',function($Q)use($st){ $Q->where('code','like',$st)->orWhere('name','like',$st); })->orWhereHas('User',function($Q)use($st){ $Q->where('code','like',$st)->orWhere('name','like',$st); })->orWhereHas('Responder',function($Q)use($st){ $Q->where('code','like',$st)->orWhere('name','like',$st); })->orWhere('message','like',$st)->orWhere('response','like',$st); }); } @endphp
@php $Data = $ORM->paginate(30); @endphp

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Service Requests</strong>@foreach(array_diff_key($SReq->actions,array_fill_keys($SReq->list_actions,'-')) as $action) {!! PanelHeadAddButton(Route('sreq.'.$action),$SReq->action_title[$action]) !!} @if($loop->remaining) <div class="pull-right">&nbsp;</div> @endif @endforeach</div>
		<div class="panel-body">
			<div class="clearfix pagination">
				<div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchText()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
				<div class="pull-right">{{ $Data->appends(['search_text' => Request()->search_text])->links() }}</div>
			</div>@if($Data->isNotEmpty())
			<div class="table table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Support Team</th><th>User</th><th>Message</th><th>Response</th><th>Responder</th><th>Actions</th></tr></thead><tbody>
				@foreach($Data as $Obj)
				<tr><th>{{ $loop->iteration }}</th><td>{{ $Obj->Supportteam->name }}</td><td>{{ $Obj->User->name }}<br><small>({{ date('d/M/y h:i a',$Obj->user_time) }})</small></td><td>{!! nl2br($Obj->message) !!}</td><td>{!! nl2br($Obj->response) !!}<br>@if($Obj->ticket) <small><a href="{{ Route('ticket.panel',$Obj->ticket) }}">{{ $Obj->ticket }}</a></small> @endif</td><td>@if ($Obj->responder) {{ $Obj->Responder->name }} <br> <small>({{ $Obj->Responder->Roles->implode('displayname',', ') }})</small> <br><small>({{ date('d/M/y h:i a',$Obj->time) }})</small> @endif</td><td>{!! ActionsToListIcons($Obj,'available_actions') !!}</td></tr>
				@endforeach
			</tbody></table></div>
			@else <div class="jumbotron text-center"><h4>No records found!</h4></div> @endif
		</div>
	</div>
</div>

@endsection
@php
function ActionsToListIcons($Obj,$Prop,$Pref = 'sreq',$PK = 'id',$Title = 'action_title',$Icon = 'action_icon'){
	$lacs = $Obj->_GETARRAYVALUES($Obj->actions,$Obj->list_actions);
	$LI = []; foreach($Obj->$Prop as $act){
		if(!in_array($act,$lacs)) continue;
		$LI[] = glyLink(Route($Pref.'.'.$act,[$Obj->$PK]),$Obj->$Title[$act],$Obj->$Icon[$act],['class' => 'btn', 'attr' => 'style="padding:6px 4px;"']);
	}
	return implode(' ',$LI);
}
@endphp