@extends("tsa.page")
@include('BladeFunctions')
@section("content")
@php $Data = new \App\Models\TechnicalSupportAgent(); @endphp

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Support Agents</strong>{!! glyLink(Route('tsa.create'),'Create New Agent','plus', ['text'=>' Create New Agent', 'class'=>'btn btn-info btn-sm pull-right']) !!}</div>
		<div class="panel-body">@if($Data->count())
			<div class="table-responsive">
				<table class="table table-bordered">
					<thead><tr><th>No</th><th>Code</th><th>Name</th><th>Phone</th><th>Email</th><th>Actions</th></tr></thead>
					<tbody>@foreach($Data->all() as $D)
						<tr>
							<td>{{ $loop->iteration }}</td><td>{{ $D->code }}</td><td>{{ $D->name }}</td><td>+{{ $D->Details->phonecode }}-{{ $D->Details->phone }}</td><td>{{ $D->Logins->implode('email', ', ') }}</td>
							<td>
								{!! glyLink(Route('tsa.show',['code'=>$D->code]), 'View Details', 'list-alt', ['class'	=>	'btn btn-none']) !!} 
								{!! glyLink(Route('tsa.edit',['code'=>$D->code]), 'Edit', 'edit', ['class'	=>	'btn btn-none']) !!}
								{!! glyLink(Route('tsa.login_reset',['code'=>$D->code]), 'Sent login reset mail information', 'log-in', ['class'	=>	'btn btn-none']) !!}
								{!! glyLink(Route('tsa.tkt.prv',['code'=>$D->code]), 'Update privilages on Ticket ', 'tags', ['class'	=>	'btn btn-none']) !!}
								{!! glyLink('javascript:DeleteTSA(\''.Route('tsa.delete',$D->code).'\')', 'Remove Support Agent', 'remove', ['class'	=>	'btn btn-none']) !!}
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
@push('js')
<script type="text/javascript">
function DeleteTSA(url){
	if(confirm('Are you sure, you want to delele this Support Agent??')) location.href = url;
}
</script>
@endpush