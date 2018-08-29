@extends("dbb.page")
@include('BladeFunctions')
@section("content")
@php $ORM = \App\Models\DatabaseBackup::whereStatus('WITHIN'); @endphp
@php if(Request()->search_text != ""){ $st = '%'.Request()->search_text.'%'; $ORM->where(function($Q)use($st){ $Q->where('details','like',$st)->orWhereHas('Customer',function($Q)use($st){ $Q->where('name','like',$st)->orWhere('code','like',$st)->orWhereHas('Details',function($Q)use($st){ $Q->where('phone','like',$st); })->orWhereHas('Logins',function($Q)use($st){ $Q->where('email','like',$st); }); }); }); } @endphp
@php $Data = $ORM->paginate(30); @endphp

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Database Backups</strong>{!! PanelHeadAddButton(Route('dbb.upload'),'Upload new backup') !!}</div>
		<div class="panel-body">
			<div class="clearfix pagination">
				<div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchText()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
				<div class="pull-right">{{ $Data->appends(['search_text' => Request()->search_text])->links() }}</div>
			</div>@if($Data->isNotEmpty())
			<div class="table table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Customer</th><th>Details</th><th>Uploaded</th><th>File</th>@if(in_array(session()->get('_rolename'),['supportteam','supportagent','company']))<th>Actions</th>@endif</tr></thead><tbody>
				@foreach($Data as $Obj)
				<tr><th>{{ $loop->iteration }}</th><td>{{ $Obj->Customer->name }}<br><small>({{ $Obj->Customer->code }})</small></td><td>{{ $Obj->details }}</td><td>By: {{ $Obj->User->name }}<br>On: <strong>{{ date('d/M/y h:i a',strtotime($Obj->created_at)) }}</strong></td><td>Size: <strong>{{ GetReadableSize($Obj->size) }}</strong><br>Extension: {{ $Obj->format }}</td>@if(in_array(session()->get('_rolename'),['supportteam','supportagent','company']))<td>{!! glyLink($Obj->download_link, 'Download this backup', 'download', ['class' => 'btn']) !!}</td>@endif</tr>
				@endforeach
			</tbody></table></div>
			@else <div class="jumbotron text-center"><h4>No records found!</h4></div> @endif
		</div>
	</div>
</div>

@endsection
@php
function GetReadableSize($size){
	$U = ['B','KB','MB','GB','TB']; $rs = $size; $C = 0;
	while($rs >= 1024){
		$rs = $rs/1024;
		$C++;
	}
	return join(" ",[round($rs,2),$U[$C]]);
}
@endphp