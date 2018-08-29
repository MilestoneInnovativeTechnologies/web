@extends("ppo.page")
@include('BladeFunctions')
@section("content")
@php $Data = \App\Models\PublicPrintObject::find($code); @endphp

<div class="content">
	<form method="post" enctype="multipart/form-data">{{ csrf_field() }}
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Add/Update Print Object Preview</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('ppo.index'):url()->previous()) !!}</div>
			<div class="panel-body">
				<div class="col-xs-6">
					{!! formGroup(2,'preview','file','Preview File') !!}
				</div>
				<div class="col-xs-6"><h4><u>Current Preview</u></h4><img class="img-responsive" src="{{ \Storage::disk($Data->storage_disk)->url($Data->preview) }}"></div>
			</div>
			<div class="panel-footer clearfix">
				<input type="submit" name="submit" value="Upload Preview" class="btn btn-primary pull-right">
			</div>
		</div>
	</form>
</div>

@endsection