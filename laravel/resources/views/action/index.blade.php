@extends("action.page")
@section("content")

<div class="clearfix">
	<a href="{{ Route('action.create') }}" class="btn btn-info pull-right btn-lg"><span class="glyphicon glyphicon-plus"></span> Create New</a>
	<br><br><br>
</div>

<div class="table-responsive">
	@if(!empty($Items))
	<table class="table table-bordered table-stripped">
		<tr><th>No</th><th>ID</th><th>Display Name</th><th>Base Name</th><th>Description</th><th class="text-center">Actions</th></tr>
		@foreach($Items as $I => $ItemObj)
			<tr>
				<th width="3%" class="text-center">{{ $I+1 }}</th><td width="7%">{{ $ItemObj["id"] }}</td><td width="10%">{{ $ItemObj["displayname"] }}</td><td width="10%">{{ $ItemObj["name"] }}</td><td width="55%">{{ $ItemObj["description"] }}</td>
				<td width="15%">
					<a href="{{ Route('action.show',['role'=>$ItemObj["id"]]) }}" title="View details of {{ $ItemObj["displayname"] }}" class="btn"><span class="glyphicon glyphicon-list-alt"></span></a>
					<a href="{{ Route('action.edit',['role'=>$ItemObj["id"]]) }}" title="Edit details of {{ $ItemObj["displayname"] }}" class="btn"><span class="glyphicon glyphicon-edit"></span></a>
				</td>
			</tr>
		@endforeach
	</table>
	@else
	<div class="jumbotron">
		<h1 class="text-center">No Records found</h1>
		<p class="text-center"><a href="{{ Route('action.create') }}" class="btn btn-primary text-center">Create New</a></p>
		
	</div>
	@endif
</div>

@endsection