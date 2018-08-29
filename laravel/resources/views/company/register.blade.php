@extends("company.page")
@section("content")

<div class="content">
	<div class="clearfix">
		<a href="{{ url()->previous() }}" class="btn btn-default"><span class="glyphicon glyphicon-arrow-left"></span> Back</a><br><br>
	</div><form action="{{ Route('customer.registration',['customer'=>$Data['customer']['code'],'seqno'=>$Data['seqno']]) }}" method="post" class="form-horizontal">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Register</strong></div>
		<div class="panel-body">
			<div class="row">
				<div class="col col-md-6">
					<div class="form-group">
						<label class="control-label col-xs-3">Requisition No</label>
						<div class="col-xs-9">
							<input type="text" class="form-control" disabled value="{{ $Data['requisition'] }}">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-xs-3">Serial No</label>
						<div class="col-xs-9">
							<input type="text" name="serialno" class="form-control" required autocomplete="off">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-xs-3">Registration Key</label>
						<div class="col-xs-9">
							<input type="text" name="key" class="form-control" required autocomplete="off">
						</div>
					</div>
					<div class="form-group">
						<div class="col-xs-12">
							<input type="submit" name="submit" value="Submit Key" class="btn btn-primary pull-right">
						</div>
					</div>
				</div>
				<div class="col col-md-6" style="border-left: 1px solid #DDD">
					<div class="table-responsive">
						<table class="table table-striped">
							<tbody>
								<tr><th class="text-right">Software</th><th>:</th><td>{{ $Data['product']['name'] }} {{ $Data['edition']['name'] }} Edition</td></tr>
								<tr><th class="text-right">Version</th><th>:</th><td>{{ $Data['version'] }}</td></tr>
								<tr><th class="text-right">Database</th><th>:</th><td>{{ $Data['database'] }}</td></tr>
								<tr><th class="text-right">Company</th><th>:</th><td>{{ $Data['customer']['name'] }}</td></tr>
								<tr><th class="text-right">Industry</th><th>:</th><td>{{ ($Data['customer']['industry'])?$Data['customer']['industry'][0]['name']:'' }}</td></tr>
								<tr><th class="text-right">Email</th><th>:</th><td>{{ $Data['customer']['logins'][0]['email'] }}</td></tr>@php $Details = $Data['customer']['details']; @endphp
								<tr><th class="text-right">Phone</th><th>:</th><td>{{ "+".$Details['phonecode']."-".(($Details['phone'])?:"") }}</td></tr>
								<tr><th class="text-right">Address</th><th>:</th><td>{{ $Details['address1'].", ".$Details['address2'] }}<br>{{ ($Details['city'])?($Details['city']['name'].", ".$Details['city']['state']['name']):'' }}<br>{{ ($Details['city'])?($Details['city']['state']['country']['name']):'' }}</td></tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div></form>
</div>

@endsection