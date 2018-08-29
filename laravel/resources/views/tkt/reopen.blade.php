@extends("tkt.page")
@include('BladeFunctions')
@section("content")
@php $Data = \App\Models\Ticket::with('Category','Type','Customer','Cstatus','Product','Edition')->whereCode(Request()->tkt)->first() @endphp

<div class="content"><form method="post">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Reopen Ticket - {{ $Data->code }}</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('tkt.index',['tkt'=>$Data->code]):url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col col-md-6">
					{!! formGroup(1,'status_text','textarea','Please mention reason to REOPEN this ticket',['style' => 'height: 160px']) !!}
				</div>
				<div class="col col-md-6">
					<table class="table table-striped"><tbody>
						<tr><th>Title</th><td>{{ $Data->title }}</td></tr>
						<tr><th>Description</th><td>{{ $Data->description }}</td></tr>
						<tr><th>Closed at</th><td>{{ date('h:i A, D-d/M/y',strtotime($Data->Cstatus->created_at)) }}</td></tr>
					</tbody></table>
				</div>
			</div>
		</div>
		<div class="panel-footer clearfix">
			<input type="submit" name="submit" value="REOPEN Ticket" class="btn btn-primary pull-right">
		</div>
	</div></form>
</div>

@endsection