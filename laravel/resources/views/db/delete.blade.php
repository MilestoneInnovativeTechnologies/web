@extends("db.page")
@include('BladeFunctions')
@section("content")

<div class="content"><form method="post">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Confirm deleteing the branding details</strong>{!! PanelHeadBackButton(url()->previous()) !!}</div>
		<div class="panel-body">@include('db.view_inc')
		</div>
		<div class="panel-footer clearfix">
			<input type="submit" name="submit" value="Confirm Delete" class="btn btn-danger pull-right">
		</div>
	</div></form>
</div>

@endsection