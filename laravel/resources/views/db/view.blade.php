@extends("db.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Details of Branding</strong>{!! PanelHeadBackButton(url()->previous()) !!}</div>
		<div class="panel-body">@include('db.view_inc')
		</div>
	</div>
</div>

@endsection