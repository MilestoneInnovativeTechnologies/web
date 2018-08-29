@extends("st.page")
@include('BladeFunctions')
@section("content")
@php $Data = new \App\Models\SupportType(); @endphp
<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Support Types</strong>{!! glyLink(Route('st.create'),'Add New Support Type','plus', ['text'=>' Add New Support Type', 'class'=>'btn btn-info btn-sm pull-right']) !!}</div>
		<div class="panel-body">@if($Data->count())
			<div class="table-responsive">
				<table class="table table-bordered">
					<thead><tr><th>No</th><th>Code</th><th>Name</th><th>Description</th><th>Status</th><th>Action</th></tr></thead>
					<tbody>@foreach($Data->all() as $D)
						<tr>
							<td>{{ $loop->iteration }}</td><td>{{ $D->code }}</td><td>{{ $D->name }}</td><td>{{ $D->description }}</td><td>{{ $D->status }}</td>
							<td nowrap>
								{!! glyLink(Route('st.show',['supportType'	=>	$D->code]),'View '.$D->name,'list-alt',['class'=>'btn']) !!}
								{!! glyLink(Route('st.edit',['supportType'	=>	$D->code]),'Edit '.$D->name,'edit',['class'=>'btn']) !!}
								@if($D->status == "ACTIVE")
									{!! glyLink(Route('st.delete',['supportType'	=>	$D->code]),'Delete '.$D->name,'remove',['class'=>'btn']) !!}
								@else
									{!! glyLink(Route('st.undelete',['supportType'	=>	$D->code]),'Make status ACTIVE for '.$D->name,'flash',['class'=>'btn']) !!}
								@endif
							</td>
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