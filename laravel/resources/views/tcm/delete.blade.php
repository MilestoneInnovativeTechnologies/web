@extends("tcm.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="col col-md-8 col-md-offset-2"><form method="post">{{ csrf_field() }}
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Delete Ticket Category</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('tcm.index'):url()->previous()) !!}</div>
			<div class="panel-body"><div class="table-responsive"><table class="table table-striped"><tbody>
				<tr><th>Name</th><th>:</th><td>{{ $tcm->name }}</td></tr>
				<tr><th>Description</th><th>:</th><td>{!! nl2br($tcm->description) !!}</td></tr>
				<tr><th>Priority</th><th>:</th><td>{{ $tcm->priority }}</td></tr>
				<tr><th>Available</th><th>:</th><td>{{ $tcm->available_field_options[$tcm->available] }}</td></tr>
			</tbody></table></div>
			</div>
			<div class="panel-footer clearfix">
				<input type="submit" name="submit" value="Delete Category Now" class="btn btn-primary pull-right">
			</div>
		</div>
	</form></div>
</div>

@endsection