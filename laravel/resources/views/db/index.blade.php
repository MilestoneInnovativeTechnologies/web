@extends("db.page")
@include('BladeFunctions')
@section("content")
@php $orm = \App\Models\DistributorBranding::latest()->with('Branding'); $Data = $orm->paginate(15); $Links = $Data->links(); @endphp

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Distributor Brandings</strong>{!! PanelHeadAddButton(Route('db.new'),' Add new Branding details') !!}</div>
		<div class="panel-body">
			<div class="clearfix pagination">
				<div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search by distributor, domain, name" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchText()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
				<div class="pull-right">{!! $Links !!}</div>
			</div>@if($Data->count())
			<div class="table table-responsive"><table class="table table-bordered"><thead><tr><th>Distributor</th><th>Domain</th><th>Name</th><th>Actions</th></tr></thead><tbody>
			@foreach($Data->groupBy('distributor') as $dist => $Ary)
				<tr>
					<td rowspan="{{ count($Ary) }}">{{ $Ary[0]->Distributor->name }}<br><small>({{ $dist }})</small></td>
					@foreach($Ary as $R)
					<td>{{ $R->domain }}</td>
					<td>{{ $R->Branding->name }}</td>
					<td>{!! GetActions($R) !!}</td>
					@if($loop->remaining) </tr><tr> @endif
					@endforeach
				</tr>
			@endforeach
			</tbody></table></div>@else
			<div class="jumbotron">
				<h2 class="text-center">No Records found</h2>
			</div>@endif
		</div>
		<div class="panel-footer clearfix"></div>
	</div>
</div>

@endsection
@php
function GetActions($Obj){
	return implode('',[
		glyLink(Route('db.view',$Obj->id),'View details','list-alt',['class' => 'btn']),
		glyLink(Route('db.add_domain',$Obj->id),'Add one more domain','tasks',['class' => 'btn']),
		glyLink(Route('db.edit',$Obj->id),'Edit details','edit',['class' => 'btn']),
		glyLink(Route('db.delete',$Obj->id),'Delete','remove',['class' => 'btn']),
	]);
}
@endphp