@extends("tsa.page")
@include('BladeFunctions')
@section("content")
@php $Agent = \App\Models\TechnicalSupportAgent::find(Request()->tsa); @endphp

<div class="content">
	<div class="row">
		<div class="col-md-8 col-md-offset-2"><form method="post">{{ csrf_field() }}
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
							</tbody>
						</table>
					</div>
				</div>
				<div class="panel-footer clearfix">
					<h4 class="pull-left">You are about to send Login Reset Mail to {{ $Agent->name }}</h4>
					<input type="submit" name="submit" value="Send Login Reset Mail" class="btn btn-primary pull-right">
				</div>
			</div>
		</form></div>
	</div>
</div>

@endsection