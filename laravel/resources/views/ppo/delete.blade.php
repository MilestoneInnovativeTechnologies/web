@extends("ppo.page")
@include('BladeFunctions')
@section("content")
@php $ORM = new \App\Models\PublicPrintObject; @endphp

<div class="content">
	<form method="post" enctype="application/x-www-form-urlencoded">{{ csrf_field() }}
		<div class="panel panel-default">
			<div class="panel-heading"><strong>You are about to delete a Print Object</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('ppo.index'):url()->previous()) !!}</div>
			<div class="panel-body">
				<h3>Are you sure, you want to delete, <u>{{ $ORM->find($code)->name }}</u></h3>
			</div>
			<div class="panel-footer clearfix">
				<input type="submit" name="submit" value="Yes Delete" class="btn btn-warning pull-right">
			</div>
		</div>
	</form>
</div>

@endsection