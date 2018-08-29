@extends("partner.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>{{ $Partner->name }}</strong>{!! glyLink(url()->previous(),'Go Back','arrow-left', ['text'=>' Back', 'class'=>'btn btn-default btn-sm pull-right']) !!}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-md-6">
					<div class="table table-responsive">
						<table class="table table-striped">
							<thead></thead>
							<tbody>@php $Details = $Partner->Details @endphp
								<tr><th>Code</th><td>{{ $Partner->code }}</td></tr>
								<tr><th>Name</th><td>{{ $Partner->name }}</td></tr>
								<tr><th>Status</th><td>{{ $Partner->status }}</td></tr>
								<tr><th>Status Description</th><td>{{ $Partner->status_description }}</td></tr>
								<tr><th>Phone</th><td>+{{ $Details->phonecode }}-{{ $Details->phone }}</td></tr>
							</tbody>
						</table><table class="table table-striped">
							<thead><tr><th colspan="2">Email and Roles</th></tr></thead>
							<tbody>@php $Logins = $Partner->Logins @endphp
								@foreach($Logins as $Login)
								@php $Roles = $Login->Roles @endphp
								<tr><td rowspan="{{ $Roles->count() }}" valign="middle">{{ $Login->email }}</td><td>
									@if($Roles->isNotEmpty())
										@foreach($Roles as $Role)
										{{ ucwords($Role->rolename) }}
										{!! ($loop->last)?'':'</td></tr><tr><td>' !!}
										@endforeach
									@endif
								</td></tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
				<div class="col-md-6">
					<div class="table table-responsive">
						<table class="table table-striped">
							<thead></thead>
							<tbody>
								<tr><th>Address</th><td>{{ $Details->address1 }}, {{ $Details->address2 }}, {{ ($Details->city)?($Details->City->name . ", " . $Details->City->State->name . ", " . $Details->City->State->Country->name):'' }}</td></tr>
								<tr><th>Industry</th><td>{{ ($Details->Industry)?$Details->Industry->name:'' }}</td></tr>
								<tr><th>Price List</th><td>{{ ($Details->Pricelist)?$Details->Pricelist->name:'' }}</td></tr>
								<tr><th>Currency</th><td>{{ $Details->currency }}</td></tr>
								<tr><th>Website</th><td>{{ $Details->website }}</td></tr>
							</tbody>
						</table><table class="table table-striped">
							<thead><tr><th colspan="2">Parent Details</th></tr></thead>
							<tbody>@php $Parent = $Partner->Parent->ParentDetails @endphp
								<tr><td colspan="2">
									<strong>{{ $Parent->name }}</strong><br>@php $PD = $Parent->Details @endphp
									{{ $PD->address1 }}, {{ $PD->address2 }}, {{ ($PD->city)?($PD->City->name . ', ' . $PD->City->State->name . ', ' . $PD->City->State->Country->name):'' }}
								</td></tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection