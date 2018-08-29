@extends("notification.page")
@include('BladeFunctions')
@section("content")
@php $ORM = new \App\Models\Notification; @endphp
@php if(Request()->search_text){ $st = '%'.Request()->search_text.'%'; $ORM = $ORM->where(function($Q)use($st){ $Q->where('code','like',$st)->orWhere('title','like',$st)->orWhere('description_short','like',$st)->orWhere('description','like',$st); }); } @endphp
@php $Data = $ORM->paginate(30); @endphp

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Notifications</strong>@if(in_array('new',(new \App\Models\Notification)->available_actions)) {!! PanelHeadAddButton(Route('notification.new'),'Create New Notification') !!} @endif</div>
		<div class="panel-body">
			<div class="clearfix pagination">
				<div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchText()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
				<div class="pull-right">{{ $Data->appends(['search_text' => Request()->search_text])->links() }}</div>
			</div>@if($Data->isNotEmpty())
			<div class="table table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Details</th><th>Date</th><th>Target</th><th>Actions</th></tr></thead><tbody>
				@foreach($Data as $Obj)
				<tr><th>{{ $loop->iteration }}</th><td><small>{{ $Obj->code }}</small><br><strong>{{ $Obj->title }}</strong><br><small>{!! nl2br(mb_substr($Obj->description_short,0,168)) !!}</small></td><td nowrap><strong>Published: </strong><small>{{ date('d/M/y',strtotime($Obj->date)) }}</small><br><strong>Notify From: </strong><small>{{ date('d/M/y',strtotime($Obj->notify_from)) }}</small><br><strong>Notify Till: </strong><small>{{ date('d/M/y',strtotime($Obj->notify_to)) }}</small><br></td><td nowrap>@if($Obj->target) {{ ($Obj->target_type != 'Only')?'All ':'Only afew ' }} {{ ucwords($Obj->target) }}s @if($Obj->target_type == 'Except') except afew @endif @else NONE @endif</td><td width="120">{!! ActionsToListIcons($Obj,'available_actions') !!}</td></tr>
				@endforeach
			</tbody></table></div>
			@else <div class="jumbotron text-center"><h4>No records found!</h4></div> @endif
		</div>
	</div>
</div>

@endsection
@php
function ActionsToListIcons($Obj,$Prop = 'available_actions',$Pref = 'notification',$PK = 'code',$Title = 'action_title',$Icon = 'action_icon',$Modal = 'modal_actions'){
	$LI = [];
	foreach($Obj->$Prop as $act){
		if(in_array($act,$Obj->$Modal)) continue;
		$LI[] = glyLink(Route($Pref.'.'.$act,[$Obj->$PK]),$Obj->$Title[$act],$Obj->$Icon[$act],['class' => 'btn', 'attr' => 'style="padding:6px 6px;"']);
	}
	return implode('',$LI);
}
@endphp