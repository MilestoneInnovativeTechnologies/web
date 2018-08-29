@extends("db.page")
@include('BladeFunctions')
@section("content")

<div class="content"><form method="post">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Add More Domains</strong>{!! PanelHeadBackButton(url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="clearfix">
				<div class="col-xs-6">{!! formGroup(2,'domain','text','New Domain Name',old('domain'),['labelWidth' => 4]) !!}</div>
				<div class="col-xs-6"><input type="submit" name="submit" value="Add Domain" class="btn btn-info"></div>
			</div>
		<hr>
		@include('db.view_inc')
		</div>
	</div></form>
</div>

@endsection