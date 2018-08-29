@extends("role.page")
@section("content")

<div class="clearfix">
	<a href="{{ Route('role.create')}}" class="btn btn-info pull-right btn-lg"><span class="glyphicon glyphicon-plus"></span> Create New</a>
	<br><br><br>
</div>

<div class="table-responsive">
	@if(!empty($Roles))
	<table class="table table-bordered table-stripped">
		<tr><th>No</th><th>Code</th><th>Display Name</th><th>Base Name</th><th>Description</th><th class="text-center">Actions</th></tr>
		@foreach($Roles as $I => $RoleObj)
			<tr>
				<th width="3%" class="text-center">{{ $I+1 }}</th><td width="7%">{{ $RoleObj["code"] }}</td><td width="10%">{{ $RoleObj["displayname"] }}</td><td width="10%">{{ $RoleObj["name"] }}</td><td width="55%">{{ $RoleObj["description"] }}</td>
				<td width="15%">
					<a href="{{ Route('role.resource',['role'=>$RoleObj["code"]]) }}" title="Resource details of {{ $RoleObj["displayname"] }}" class="btn"><span class="glyphicon glyphicon-link"></span></a>
					<a href="{{ Route('role.show',['role'=>$RoleObj["code"]]) }}" title="View details of {{ $RoleObj["displayname"] }}" class="btn"><span class="glyphicon glyphicon-list-alt"></span></a>
					<a href="{{ Route('role.edit',['role'=>$RoleObj["code"]]) }}" title="Edit details of {{ $RoleObj["displayname"] }}" class="btn"><span class="glyphicon glyphicon-edit"></span></a>
					<form style="display:inline" method="post" action="{{ Route('role.destroy',['role'=>$RoleObj["code"]]) }}">{{ method_field('DELETE') }}{{ csrf_field() }}<button type="submit" class="btn btn-none" style="background: none; border: none; color: #337ab7"><span class="glyphicon glyphicon-remove"></span></button></form>
				</td>				
			</tr>
		@endforeach
	</table>
	@else
	<div class="jumbotron">
		<h1 class="text-center">No Records found</h1>
		<p class="text-center"><a href="role/create" class="btn btn-primary text-center">Create New</a></p>
		
	</div>
	@endif
</div>

@endsection