@extends("tkt.page")
@include('BladeFunctions')
@section("content")
@php $Data = \App\Models\Ticket::with('Customer','CreatedBy')->whereCode(Request()->tkt)->first() @endphp

<div class="content"><form method="post">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Dismiss Ticket - {{ $Data->code }}</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('tkt.index',['tkt'=>$Data->code]):url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col col-md-6">
					<h4>You are about to Dismiss this ticket, Please provide a valid reason</h4>
					<textarea name="status_text" class="form-control" style="height: 160px;"></textarea>
				</div>
				<div class="col col-md-6">
					<table class="table table-striped"><tbody>
						<tr><th>Title</th><td>{{ $Data->title }}</td></tr>
						<tr><th>Description</th><td>{!! nl2br($Data->description) !!}</td></tr>
						<tr><th>Customer</th><td>{{ $Data->Customer->name }}</td></tr>
						<tr><th>Created at</th><td>{{ date('h:i A, D-d/M/y',strtotime($Data->created_at)) }}</td></tr>
						<tr><th>Created by</th><td>{{ $Data->CreatedBy->name }}</td></tr>
					</tbody></table>
				</div>
			</div>
		</div>
		<div class="panel-footer clearfix">
			<input type="submit" name="submit" value="Dismiss Ticket" class="btn btn-primary pull-right">
		</div>
	</div></form>
</div>

@endsection