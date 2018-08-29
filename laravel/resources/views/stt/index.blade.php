@extends("stt.page")
@include('BladeFunctions')
@section("content")
@php $Data = new \App\Models\TicketType(); @endphp
<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Ticket Types</strong>{!! glyLink(Route('stt.create'),'Add New Ticket Type','plus', ['text'=>' Add New Ticket Type', 'class'=>'btn btn-info btn-sm pull-right']) !!}</div>
		<div class="panel-body">@if($Data->count())
			<div class="table-responsive">
				<table class="table table-bordered">
					<thead><tr><th>No</th><th>Code</th><th>Name</th><th>Description</th><th>Status</th><th>Action</th></tr></thead>
					<tbody>@foreach($Data->all() as $D)
						<tr>
							<td>{{ $loop->iteration }}</td><td>{{ $D->code }}</td><td>{{ $D->name }}</td><td>{{ $D->description }}</td><td>{{ $D->status }}</td>
							<td nowrap>
								{!! glyLink(Route('stt.show',['ticketType'	=>	$D->code]),'View '.$D->name,'list-alt',['class'=>'btn']) !!}
								{!! glyLink(Route('stt.edit',['ticketType'	=>	$D->code]),'Edit '.$D->name,'edit',['class'=>'btn']) !!}
								@if($D->status == "ACTIVE")
									{!! glyLink(Route('stt.delete',['ticketType'	=>	$D->code]),'Delete '.$D->name,'remove',['class'=>'btn']) !!}
								@else
									{!! glyLink(Route('stt.undelete',['ticketType'	=>	$D->code]),'Make status ACTIVE for '.$D->name,'flash',['class'=>'btn']) !!}
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