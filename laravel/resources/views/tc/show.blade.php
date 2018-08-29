@extends("tc.page")
@include('BladeFunctions')
@section("content")
@php $Fields = ['code','name','description','Parent Category'=>'parent','Child Level'=>'level','Priority of tickets'=>'priority','available'] @endphp

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>{{ $Data->name }}</strong>{!! glyLink(url()->previous(),'Go Back','arrow-left', ['text'=>' Back', 'class'=>'btn btn-default btn-sm pull-right']) !!}</div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-striped">
					<tbody>
						@foreach($Fields as $Key => $Field)
						<tr><td width="18%"><strong>{{ is_numeric($Key) ? ucwords(str_replace('_',' ',$Field)) : $Key }}</strong></td><td>{{ ($Field == 'parent' && $Data->parent)?$Data->Parent->name:$Data->$Field }}</td></tr>
						@endforeach
						<tr><td><strong>Created at</strong></td><td>{{ date("d/M/Y",strtotime($Data->created_at)) }}</td></tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection
