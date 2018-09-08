@extends("sreq.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="col col-md-8 col-md-offset-2"><form method="post">{{ csrf_field() }}
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Delete Service Request</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('sreq.index'):url()->previous()) !!}</div>
			<div class="panel-body"><div class="table-responsive"><table class="table table-striped"><tbody>
				<tr><th>Support Team</th><th>:</th><td>{{ $sr->Supportteam->name }}</td></tr>
				<tr><th>Message</th><th>:</th><td>{!! nl2br($sr->message) !!}</td></tr>
			</tbody></table></div>
			</div>
			<div class="panel-footer clearfix">
				<input type="submit" name="submit" value="Delete" class="btn btn-primary pull-right">
			</div>
		</div>
	</form></div>
</div>

@endsection