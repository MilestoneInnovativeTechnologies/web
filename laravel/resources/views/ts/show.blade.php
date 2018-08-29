@extends("ts.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>{{ $Data->name }}</strong>{!! glyLink(url()->previous(),'Go Back','arrow-left', ['text'=>' Back', 'class'=>'btn btn-default btn-sm pull-right']) !!}</div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-striped">
					<tbody>
						@foreach(['code','name','description','customer_side_view','agent_status','customer_status'] as $Field)
						<tr><td width="18%"><strong>{{ ucwords(str_replace('_',' ',$Field)) }}</strong></td><td>{{ $Data->$Field }}</td></tr>
						@endforeach
						<tr><td><strong>Comes after status</strong></td><td>{{ ($Data->After)?$Data->After->name:'' }}</td></tr>
						<tr><td><strong>Similiar to status</strong></td><td>{{ ($Data->Similiar)?$Data->Similiar->name:'' }}</td></tr>
						<tr><td><strong>Created at</strong></td><td>{{ date("d/M/Y",strtotime($Data->created_at)) }}</td></tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection