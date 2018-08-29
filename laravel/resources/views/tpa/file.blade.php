@extends("tpa.page")
@include('BladeFunctions')
@section("content")
@php
$Data = \App\Models\ThirdPartyApplication::find(Request()->code);
@endphp

<div class="content"><form method="post" enctype="multipart/form-data">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Add/Update File</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('tpa.index'):url()->previous()) !!}</div>
		<div class="panel-body">
			{!! formGroup(2,'filename','text','Filename',$Data->file) !!}
			{!! formGroup(2,'size','text','Size',$Data->size) !!}
			{!! formGroup(2,'extension','text','Extension',$Data->extension) !!}
			{!! formGroup(2,'filename','static','OR') !!}
			{!! formGroup(2,'file','file','Upload') !!}
		</div>
		<div class="panel-footer clearfix">
			<input type="submit" name="submit" value="Update" class="btn btn-primary pull-right">
		</div>
	</div></form>
</div>

@endsection