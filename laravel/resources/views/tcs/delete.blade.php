@extends("tcs.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="col col-md-8 col-md-offset-2"><form method="post">{{ csrf_field() }}
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Delete Category Specification</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('tcs.index'):url()->previous()) !!}</div>
			<div class="panel-body"><div class="table-responsive"><table class="table table-striped"><tbody>
				<tr><th>Name</th><th>:</th><td>{{ $tcs->name }}</td></tr>
				<tr><th>Description</th><th>:</th><td>{!! nl2br($tcs->description) !!}</td></tr>
				<tr><th>Type</th><th>:</th><td>{{ $tcs->type_field_options[$tcs->type] }}</td></tr>
			</tbody></table></div>
			</div>
			<div class="panel-footer clearfix">
				<input type="submit" name="submit" value="Delete Specification Now" class="btn btn-primary pull-right">
			</div>
		</div>
	</form></div>
</div>

@endsection