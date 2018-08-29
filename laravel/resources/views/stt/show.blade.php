@extends("stt.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>{{ $Data->name }}</strong>{!! glyLink(url()->previous(),'Go Back','arrow-left', ['text'=>' Back', 'class'=>'btn btn-default btn-sm pull-right']) !!}</div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-striped">
					<tbody>@foreach(['code','name','description','status'] as $Field)
						<tr><td width="15%"><strong>{{ ucwords(str_replace('_',' ',$Field)) }}</strong></td><td>{{ $Data->$Field }}</td></tr>
					@endforeach<tr><td width="15%"><strong>Created at</strong></td><td>{{ date("d/M/Y",strtotime($Data->created_at)) }}</td></tr></tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection