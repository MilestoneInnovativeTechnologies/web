@extends("tcm.page")
@include('BladeFunctions')
@section("content")
@php $ORM = \App\Models\TicketCategoryMaster::withoutGlobalScope('own'); @endphp
@php if(Request()->search_text){ $st = '%'.Request()->search_text.'%'; $ORM = $ORM->where(function($Q)use($st){ $Q->orWhere('name','like',$st)->orWhere('code','like',$st)->orWhere('description','like',$st)->orWhere('priority','like',$st)->orWhere('available','like',$st); }); } @endphp
@php $Data = $ORM->paginate(30); @endphp

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Ticket Categories</strong>@if(in_array('add',(new \App\Models\TicketCategoryMaster)->available_actions)) {!! PanelHeadAddButton(Route('tcm.add'),'Create New Ticket Category') !!} @endif</div>
		<div class="panel-body">
			<div class="clearfix pagination">
				<div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchText()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
				<div class="pull-right">{{ $Data->appends(['search_text' => Request()->search_text])->links() }}</div>
			</div>@if($Data->isNotEmpty())
			<div class="table table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Code &amp; Name</th><th>Description</th><th>Priority</th><th>Available</th><th>Specifications</th><th>Actions</th></tr></thead><tbody>
				@foreach($Data as $Obj)
				<tr><th>{{ $loop->iteration }}</th><td>{{ $Obj->code }}<br><strong>{{ $Obj->name }}</strong></td><td>{!! nl2br($Obj->description) !!}</td><td>{{ $Obj->priority }}</td><td>{{ $Obj->available_field_options[$Obj->available] }}</td><td>@if($Obj->specs){{ $Obj->specs->implode('name',', ') }}@endif</td><td>{!! ActionsToListIcons($Obj,'available_actions') !!}</td></tr>
				@endforeach
			</tbody></table></div>
			@else <div class="jumbotron text-center"><h4>No records found!</h4></div> @endif
		</div>
	</div>
</div>

@endsection
@php
function ActionsToListIcons($Obj,$Prop,$Pref = 'tcm',$PK = 'code',$Title = 'action_title',$Icon = 'action_icon'){
	$lacs = $Obj->_GETARRAYVALUES($Obj->actions,$Obj->list_actions);
	$LI = []; foreach($Obj->$Prop as $act){
		if(!in_array($act,$lacs)) continue;
		$LI[] = glyLink(Route($Pref.'.'.$act,[$Obj->$PK]),$Obj->$Title[$act],$Obj->$Icon[$act],['class' => 'btn', 'attr' => 'style="padding:6px 4px;"']);
	}
	return implode(' ',$LI);
}
@endphp