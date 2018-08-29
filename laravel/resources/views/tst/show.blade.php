@extends("tst.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>{{ $Data->name }}</strong>{!! glyLink(url()->previous(),'Go Back','arrow-left', ['text'=>' Back', 'class'=>'btn btn-default btn-sm pull-right']) !!}</div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-striped">
					<tbody>
						<tr><td width="18%"><strong>Team Code</strong></td><td>{{ $Data->code }}</td></tr>
						<tr><td><strong>Team Name</strong></td><td>{{ $Data->name }}</td></tr>
						<tr><td><strong>Privilaged</strong></td><td>{{ $Data->Privilage->privilage }}</td></tr>
						<tr><td><strong>Default</strong></td><td>{{ ($Data->Defaultst)?'YES':'NO' }}</td></tr>
						<tr><td><strong>Email</strong></td><td>{{ implode(", ", $Data->Logins->pluck('email')->toArray()) }}</td></tr>
						@php $D = $Data->Details @endphp
						<tr><td><strong>Phone</strong></td><td>{{ '+' . $D->phonecode . '-' . $D->phone }}</td></tr>
						<tr><td><strong>Address</strong></td><td>{{ $D->address1 . ', ' . $D->address2 }}<br>{{ $D->City->name . ', ' . $D->City->State->name }}<br>{{ $D->City->State->Country->name }}</td></tr>
						<tr><td><strong>Website</strong></td><td>{{ $D->website }}</td></tr>
						<tr><td><strong>Created at</strong></td><td>{{ date("d/M/Y",strtotime($Data->created_at)) }}</td></tr>
						<tr><td><strong>Last modified on</strong></td><td>{{ date("d/M/Y",strtotime($Data->updated_at)) }}</td></tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection