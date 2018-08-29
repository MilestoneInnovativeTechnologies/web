@extends("tsa.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>{{ $Agent->name }}</strong>{!! PanelHeadBackButton(Route('tsa.index')) !!}</div>
				<div class="panel-body">
					<div class="table table-responsive">
						<table class="table table-striped">
							<tbody>
								<tr><th>Code</th><td>{{ $Agent->code }}</td></tr>
								<tr><th>Name</th><td>{{ $Agent->name }}</td></tr>
								<tr><th>Email</th><td>{{ $Agent->Logins->implode('email', ', ') }}</td></tr>
								<tr><th>Phone</th><td>+{{ $Agent->Details->phonecode }}-{{ $Agent->Details->phone }}</td></tr>
								<tr><th>Support Team</th><td>{{ $Agent->Team->ParentDetails->name }}</td></tr>
								<tr><th>Departments</th><td>{{ $Agent->Departments->pluck('Department')->implode('name',', ') }}</td></tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection