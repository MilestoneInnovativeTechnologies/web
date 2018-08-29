@extends("ppo.page")
@include('BladeFunctions')
@section("content")
@php $Data = \App\Models\PublicPrintObject::find($code); @endphp

<div class="content">
	<form method="post" enctype="multipart/form-data">{{ csrf_field() }}
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Add/Update Print Object File</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('ppo.index'):url()->previous()) !!}</div>
			<div class="panel-body">
				<div class="col-xs-6">
					{!! formGroup(2,'file','file','Object File') !!}
				</div>
				<div class="col-xs-6">&nbsp;</div>
			</div>
			<div class="panel-footer clearfix">
				<input type="submit" name="submit" value="Upload File" class="btn btn-primary pull-right">
			</div>
		</div>
	</form>
</div>

@endsection