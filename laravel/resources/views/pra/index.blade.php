@extends("pra.page")
@include('BladeFunctions')
@section("content")
@php $ORM = new \App\Models\PrivateArticle; @endphp
@php if(Request()->search_text){ $st = '%'.Request()->search_text.'%'; $ORM = $ORM->where(function($Q)use($st){ $Q->where('name','like',$st)->orWhere('code','like',$st)->orWhere('title','like',$st); }); } @endphp
@php $Data = $ORM->paginate(30); @endphp

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Private Articles</strong>@if(in_array('new',(new \App\Models\PrivateArticle)->available_actions)) {!! PanelHeadAddButton(Route('pra.new'),'Create New Private Article') !!} @endif</div>
		<div class="panel-body">
			<div class="clearfix pagination">
				<div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchText()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
				<div class="pull-right">{{ $Data->appends(['search_text' => Request()->search_text])->links() }}</div>
			</div>@if($Data->isNotEmpty())
			<div class="table table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Name</th><th>Title &amp; View</th><th>Url</th><th>Actions</th></tr></thead><tbody>
				@foreach($Data as $Obj)
				<tr><th>{{ $loop->iteration }}</th><td><small>{{ $Obj->code }}</small><br><strong>{{ $Obj->name }}</strong></td><td>{{ $Obj->title }}<br>{{ $Obj->view }}</td><td><textarea class="form-control">{{ $Obj->Url }}</textarea></td><td>{!! ActionsToListIcons($Obj,'available_actions') !!}</td></tr>
				@endforeach
			</tbody></table></div>
			@else <div class="jumbotron text-center"><h4>No records found!</h4></div> @endif
		</div>
	</div>
</div>

@endsection
@php
function ActionsToListIcons($Obj,$Prop = 'available_actions',$Pref = 'pra',$PK = 'code',$Title = 'action_title',$Icon = 'action_icon',$Modal = 'modal_actions'){
	$LI = [];
	foreach($Obj->$Prop as $act){
		if(in_array($act,$Obj->$Modal)) continue;
		$LI[] = glyLink(Route($Pref.'.'.$act,[$Obj->$PK]),$Obj->$Title[$act],$Obj->$Icon[$act],['class' => 'btn', 'attr' => 'style="padding:6px 6px;"']);
	}
	return implode('',$LI);
}
@endphp