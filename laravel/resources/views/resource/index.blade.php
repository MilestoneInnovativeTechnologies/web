@extends("resource.page")
@section("content")

<div class="clearfix">
	<a href="{{ Route('resource.create') }}" class="btn btn-info pull-right btn-lg"><span class="glyphicon glyphicon-plus"></span> Create New</a>
	<br><br><br>
</div>

<div class="table-responsive">
	@if(!empty($Items))
	<table class="table table-bordered table-stripped">
		<tr><th>No</th><th>Code</th><th>Display Name</th><th>Base Name</th><th>Resource Actions</th><th>Description</th><th class="text-center">Actions</th></tr>
		@foreach($Items as $I => $ItemObj)
			<tr>
				<th width="3%" class="text-center">{{ $I+1 }}</th><td width="7%">{{ $ItemObj["code"] }}</td><td width="10%">{{ $ItemObj["displayname"] }}</td><td width="10%">{{ $ItemObj["name"] }}</td><td width="30%" class="resource_actions">{{ $ItemObj["action"] }}</td><td width="25%">{{ $ItemObj["description"] }}</td>
				<td width="15%">
					<a href="{{ Route('resource.role',['resource'=>$ItemObj["code"]]) }}" title="Roles for {{ $ItemObj["displayname"] }}" class="btn"><span class="glyphicon glyphicon-link"></span></a>
					<a href="{{ Route('resource.show',['resource'=>$ItemObj["code"]]) }}" title="View details of {{ $ItemObj["displayname"] }}" class="btn"><span class="glyphicon glyphicon-list-alt"></span></a>
					<a href="{{ Route('resource.edit',['resource'=>$ItemObj["code"]]) }}" title="Edit details of {{ $ItemObj["displayname"] }}" class="btn"><span class="glyphicon glyphicon-edit"></span></a>
					<form style="display:inline" method="post" action="{{ Route('resource.destroy',['code'=>$ItemObj["code"]]) }}">{{ method_field('DELETE') }}{{ csrf_field() }}<button title="Delete {{ $ItemObj["displayname"] }}" type="submit" class="btn btn-none" style="background: none; border: none; color: #337ab7"><span class="glyphicon glyphicon-remove"></span></button></form>
				</td>
			</tr>
		@endforeach
	</table>
	@else
	<div class="jumbotron">
		<h1 class="text-center">No Records found</h1>
		<p class="text-center"><a href="{{ Route('resource.create') }}" class="btn btn-primary text-center">Create New</a></p>
	</div>
	@endif
</div>

@endsection
@push("js")
<script type="text/javascript">
var TotalActions = {!! '["' . implode('","',array_column($Actions,"displayname")) . '"]' !!};
</script>
@endpush