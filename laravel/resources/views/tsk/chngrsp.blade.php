@extends("tsk.page")
@include('BladeFunctions')
@section("content")

<div class="content"><form method="post">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Change Task Responder</strong>{!! PanelHeadBackButton((url()->previous() == url()->current())?(Route('tsk.index')):(url()->previous())) !!}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col col-md-6">
					{!! formGroup(2,'responder','select','Change Responder to',$tsk->Responder?$tsk->Responder->Responder->code:'',['labelWidth'	=>	'4', 'selectOptions' => array_merge([''=>'Delete Responder'],$rsps)]) !!}
					<div class="table table-responsive">
						<table class="table table-striped">
							<tbody>
								<tr><th>Current Responder</th><td>{{ $tsk->Responder?$tsk->Responder->Responder->name:'' }}</td></tr>
								<tr><th>Support Type</th><td>{{ $tsk->support_type?$tsk->Stype->name:'' }}</td></tr>
								<tr><th>Task Title</th><td>{{ $tsk->title }}</td></tr>
								<tr><th>Description</th><td>{{ $tsk->description }}</td></tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="col col-md-6">@php $tkt = $tsk->Ticket; @endphp
					<div class="table table-responsive">
						<table class="table table-striped">
							<tbody>
								<tr><th colspan="2">Ticket Details</th></tr>
								<tr><th>Ticket Code</th><td>{{ $tkt->code }}</td></tr>
								<tr><th>Customer</th><td>{{ $tkt->Customer->name }}</td></tr>
								<tr><th>Priority</th><td>{{ $tkt->priority }}</td></tr>
								<tr><th>Title</th><td>{{ $tkt->title }}</td></tr>
								<tr><th>Description</th><td>{{ $tkt->description }}</td></tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix panel-footer">
			<input type="submit" name="submit" value="Update Responder" class="btn btn-primary pull-right">
		</div>
	</div></form>
</div>

@endsection