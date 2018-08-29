@extends("tc.page")
@include('BladeFunctions')
@section("content")
@php $Data = new \App\Models\TicketCategory() @endphp
<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Ticket Categories</strong>{!! glyLink(Route('tc.create'),'Add New Ticket Category','plus', ['text'=>' Add New Ticket Category', 'class'=>'btn btn-info btn-sm pull-right']) !!}</div>
		<div class="panel-body">@if($Data->count())
			<div class="table-responsive">
				<table class="table table-bordered">
					<thead><tr><th>No</th><th>Code</th><th>Name</th><th>Parent</th><th>Priority</th><th>Action</th></tr></thead>
					<tbody>@foreach($Data->all() as $D)
						<tr>
							<td>{{ $loop->iteration }}</td><td>{{ $D->code }}</td><td>{{ $D->name }}</td><td>{{ ($D->Parent)?$D->Parent->name:'' }}</td><td>{{ $D->priority }}</td>
							<td nowrap>
								{!! glyLink(Route('tc.show',['ticketCategory'	=>	$D->code]),'View '.$D->name,'list-alt',['class'=>'btn']) !!}
								{!! glyLink(Route('tc.edit',['ticketCategory'	=>	$D->code]),'Edit '.$D->name,'edit',['class'=>'btn']) !!}
								@if($D->status == "ACTIVE")
									{!! glyLink(Route('tc.delete',['ticketCategory'	=>	$D->code]),'Delete '.$D->name,'remove',['class'=>'btn']) !!}
								@else
									{!! glyLink(Route('tc.undelete',['ticketCategory'	=>	$D->code]),'Make status ACTIVE for '.$D->name,'flash',['class'=>'btn']) !!}
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