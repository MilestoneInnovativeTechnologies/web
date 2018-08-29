@extends("crc.page")
@include('BladeFunctions')
@section("content")
@php $ORM = \App\Models\Customer::with(['Connections'])->latest(); @endphp
@php if(Request()->search_text != ""){ $st = '%'.Request()->search_text.'%'; $ORM->where('name','like',$st)->orWhereHas('Logins',function($Q) use($st){ $Q->where('email','like',$st); })->orWhereHas('Details',function($Q) use($st){ $Q->where('phone','like',$st); }); } @endphp
@php $Data = $ORM->paginate(10) @endphp


<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Customer Connection</strong></div>
		<div class="panel-body">
			<div class="clearfix pagination">
				<div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search by name, email, phone" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchText()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
				<div class="pull-right">{!! $Data->links() !!}</div>
			</div>@if($Data->count())
			<div class="table-responsive">
				<table class="table table-bordered">
					<thead><tr><th>Name</th><th>Softwares</th><th>Action</th></tr></thead>
					<tbody>@foreach($Data as $Obj)
						<tr>
							<td>{{ $Obj->name }}</td><td>{!! GetConnections($Obj) !!}</td><td>{!! glyLink(Route('crc.create',['code'=>$Obj->code]),'Add/Edit Cookie','transfer') !!}</td>
						</tr>
					@endforeach</tbody>
				</table>
			</div>@else
			<div class="jumbotron">
				<h2 class="text-center">No Records found</h2>
			</div>@endif
		</div>
	</div>
</div>

@endsection
@push('css')
<style type="text/css">
	.pagination { margin: 0px !important; }
	.p0 { padding: 0px !important; }
</style>
@endpush
@push('js')
<script type="text/javascript">
function SearchText(){
	location.search = '?search_text='+$('[name="search_text"]').val();
}
</script>
@endpush
@php
function GetConnections($Obj){
	$Connections = $Obj->Connections;
	if($Connections->isEmpty()) return '-';
	return $Connections->implode('appname',', ');
}
@endphp