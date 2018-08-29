@extends("tkt.page")
@include('BladeFunctions')
@section("content")
@php $Data = \App\Models\Ticket::with('Category','Type','Customer.Logins','Customer.Details','Cstatus','Product','Edition')->whereCode(Request()->tkt)->first() @endphp

<div class="content"><form method="post">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Send Mail</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('tkt.index',['tkt'=>$Data->code]):url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col col-md-6">
					<h4>You are about to send the customer a mail requesting them to complete the ticket.</h4>
					<table class="table table-striped"><tbody>
						<tr><th>Customer</th><td>{{ $Data->Customer->name }}</td></tr>
						<tr><th>Email</th><td>{{ $Data->Customer->Logins[0]->email }}</td></tr>
						<tr><th>Phone</th><td>+{{ $Data->Customer->Details->phonecode }}-{{ $Data->Customer->Details->phone }}</td></tr>
					</tbody></table>
				</div>
				<div class="col col-md-6">
					<h4>Ticket Details</h4>
					<table class="table table-striped"><tbody>
						<tr><th>Title</th><td>{{ $Data->title }}</td></tr>
						<tr><th>Description</th><td>{!! nl2br($Data->description) !!}</td></tr>
						<tr><th>Created on</th><td>{{ date('h:i A, D d/M/y',strtotime($Data->created_at)) }}</td></tr>
						<tr><th>Closed at</th><td>{{ date('h:i A, D d/M/y',strtotime($Data->Cstatus->created_at)) }}</td></tr>
					</tbody></table>
				</div>
			</div>
		</div>
		<div class="panel-footer clearfix">
			<input type="submit" name="submit" value="Send Mail" class="btn btn-primary pull-right">
		</div>
	</div></form>
</div>

@endsection