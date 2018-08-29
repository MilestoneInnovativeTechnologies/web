@extends("cpo.page")
@include('BladeFunctions')
@section("content")
@php $Data = \App\Models\CustomerPrintObject::find(Request()->code); @endphp

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Customer Print Objects</strong>{!! PanelHeadBackButton((url()->previous() == url()->current())?Route('cpo.index'):url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="col-xs-10">
				@if($Data->preview && \Storage::disk('printobject')->exists($Data->preview))
				<a href="{{ \Storage::disk('printobject')->url($Data->preview) }}" target="_blank">
					<div style="border: 0px solid #DDD; background-size: contains; height:{{ getImageSize(\Storage::disk('printobject')->url($Data->preview))[1] }}px; background: url('{{ \Storage::disk('printobject')->url($Data->preview) }}') no-repeat left top" class="col-xs-12">&nbsp;</div>
				</a>@else
					No preview available!!
				@endif
			</div>
			<div class="col-xs-2">
				<form method="post" enctype="multipart/form-data">{{ csrf_field() }}
					{!! formGroup(1,'preview','file','Add New Image') !!}
					<input type="hidden" name="customer" value="{{ $Data->customer }}"><input type="hidden" name="reg_seq" value="{{ $Data->reg_seq }}">
					<input type="submit" name="Update" value="Update" class="btn btn-primary">
				</form>
			</div>
		</div>
	</div>
</div>

@endsection