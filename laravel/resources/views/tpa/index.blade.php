@extends("tpa.page")
@include('BladeFunctions')
@section("content")
@php $ORM = new \App\Models\ThirdPartyApplication; @endphp
@php if(Request()->search_text){ $st = '%'.Request()->search_text.'%'; $ORM = $ORM->where(function($Q)use($st){ $Q->where('name','like',$st)->orWhere('code','like',$st)->orWhere('title','like',$st); }); } @endphp
@php $Data = $ORM->paginate(30); @endphp

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Third Party Applications/Tools</strong>@if(in_array('new',(new \App\Models\ThirdPartyApplication)->available_actions)) {!! PanelHeadAddButton(Route('tpa.new'),'Upload New') !!} @endif</div>
		<div class="panel-body">
			<div class="clearfix pagination">
				<div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchText()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
				<div class="pull-right">{{ $Data->appends(['search_text' => Request()->search_text])->links() }}</div>
			</div>@if($Data->isNotEmpty())
			<div class="table table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th width="50%">Name &amp; Description</th><th>Details</th><th>Public</th><th>Actions</th></tr></thead><tbody>
				@foreach($Data as $Obj)
				<tr><th>{{ $loop->iteration }}</th><td><strong>{{ $Obj->name }}</strong><br><small>{!! nl2br($Obj->description) !!}</small></td><td nowrap>Version: {{ $Obj->version }}<br>Size: {{ GetReadableSize($Obj->size) }}<br>Ext: {{ $Obj->extension }}<br>Public: {{ $Obj->public }}</td><td width="15%"><textarea class="form-control">{{ $href = $Obj->download_url() }}</textarea><br><a href="{{ $href }}" target="_blank">Browse Link</a></td><td nowrap>{!! ActionsToListIcons($Obj,'available_actions') !!}</td></tr>
				@endforeach
			</tbody></table></div>
			@else <div class="jumbotron text-center"><h4>No records found!</h4></div> @endif
		</div>
	</div>
</div>

@endsection
@php
function ActionsToListIcons($Obj,$Prop = 'available_actions',$Pref = 'tpa',$PK = 'code',$Title = 'action_title',$Icon = 'action_icon',$Modal = 'modal_actions'){
	$LI = [];
	foreach($Obj->$Prop as $act){
		if(in_array($act,$Obj->$Modal)) continue;
		$LI[] = glyLink(Route($Pref.'.'.$act,[$Obj->$PK]),$Obj->$Title[$act],$Obj->$Icon[$act],['class' => 'btn', 'attr' => 'style="padding:6px 6px;"']);
	}
	return implode('',$LI);
}
function GetReadableSize($size){
	$U = ['B','KB','MB','GB','TB']; $rs = $size; $C = 0;
	while($rs >= 1024){
		$rs = $rs/1024;
		$C++;
	}
	return join(" ",[round($rs,2),$U[$C]]);
}
@endphp