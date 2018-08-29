@extends("smsg.page")
@include('BladeFunctions')
@section("content")
@php $ORM = new \App\Models\SMSGateway; @endphp
@php if(Request()->search_text){ $st = '%'.Request()->search_text.'%'; $ORM = $ORM->where(function($Q)use($st){ $Q->where('name','like',$st)->orWhere('code','like',$st); }); } @endphp
@php $Data = $ORM->paginate(30); @endphp

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>SMS Gateways</strong>@if(in_array('add',(new \App\Models\SMSGateway)->available_actions)) {!! PanelHeadAddButton(Route('smsg.add'),'Create New SMS Gateway') !!} @endif</div>
		<div class="panel-body">
			<div class="clearfix pagination">
				<div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchText()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
				<div class="pull-right">{{ $Data->appends(['search_text' => Request()->search_text])->links() }}</div>
			</div>@if($Data->isNotEmpty())
			<div class="table table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Name</th><th>Description</th><th>Arguments</th><th>Actions</th></tr></thead><tbody>
				@foreach($Data as $Obj)
				<tr><th>{{ $loop->iteration }}</th><td><strong>{{ $Obj->name }}</strong><br><small>{{ $Obj->code }}</small></td><td>{!! nl2br($Obj->Description) !!}<hr><strong>URL: </strong>{{ $Obj->url }}</td><td style="font-size: 14px">@for($i=1; 10>$i; $i++) <strong>Arg {{ $i }}</strong>:{{ $Obj->{'arg'.$i} }}@if($i%3 == 0 && $i != 9)<br>@else,<br> @endif @endfor<td>{!! ActionsToListIcons($Obj,'available_actions') !!}</td></tr>
				@endforeach
			</tbody></table></div>
			@else <div class="jumbotron text-center"><h4>No records found!</h4></div> @endif
		</div>
	</div>
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