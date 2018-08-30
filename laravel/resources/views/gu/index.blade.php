@extends("gu.page")
@include('BladeFunctions')
@section("content")
@php $ORM = \App\Models\GeneralUpload::latest('updated_at'); @endphp
@php if(Request()->search_text != ""){ $st = '%'.Request()->search_text.'%'; $ORM->where('name','like',$st)->orWhere('description','like',$st)->orWhereHas('customer',function($Q) use($st){ $Q->where('name','like',$st); }); } @endphp
@php $Data = $ORM->paginate(10); @endphp

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>General Uploads</strong>{!! PanelHeadAddButton(Route('gu.form'),'Add New Upload Form') !!}</div>
		<div class="panel-body">
			<div class="clearfix pagination">
				<div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search name, description, customer, ticket" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchText()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
				<div class="pull-right">{!! $Data->appends(['search_text' => Request()->search_text])->links() !!}</div>
			</div>@if($Data->isNotEmpty())
			<div class="table table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Name &amp; Description</th><th>Customer &amp; Ticket</th><th>File Exists &amp; Date</th><th>Overwritable</th><th>Action</th></tr></thead><tbody>
				@foreach($Data as $Obj)
				<tr><th>{{ $loop->iteration }}</th><td>{{ $Obj->name }}<br><small>{{ $Obj->description }}</small></td><td>{{ ($Obj->customer)?$Obj->Customer->name:'' }}<br>{{ ($Obj->ticket)?:'' }}</td><td>{!! ($Obj->file)?('Yes<br>'.date('D d/M/y, h:i a',$Obj->time)):'' !!}</td><td>{{ (['Y'=>'Yes','N'=>'No'])[$Obj->overwrite] }}</td><td>{!! GetActions($Obj) !!}</td></tr>
				@endforeach
			</tbody></table></div>
			@else <div class="jumbotron text-center"><h4>No records found!</h4></div> @endif
		</div>
	</div>
</div>

@endsection
@php
function GetActions($Obj){
	$Ary = [
		glyLink(Route('gu.details',$Obj->code),'View details','list-alt',['class' => 'btn']),
		glyLink(Route('gu.edit',$Obj->code),'Edit form','edit',['class' => 'btn']),
	];
	if($Obj->file) array_push($Ary,glyLink($Obj->download,'Download file','download',['class' => 'btn']));
	return implode('',$Ary);
}
@endphp