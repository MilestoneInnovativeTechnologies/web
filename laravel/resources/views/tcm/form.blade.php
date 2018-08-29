@extends("tcm.page")
@include('BladeFunctions')
@section("content")
@php
$update = [];
$fields = ['code','name','description','priority','available'];
foreach($fields as $field){
	$update[$field] = isset($tcm) ? $tcm->$field : null;
}
$TCM = new \App\Models\TicketCategoryMaster;
@endphp

<div class="content">
	<div class="col col-md-8 col-md-offset-2"><form method="post">{{ csrf_field() }}
		<div class="panel panel-default">
			<div class="panel-heading"><strong>@if(isset($tcm)) Update @else Create New @endif Category</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('tcm.index'):url()->previous()) !!}</div>
			<div class="panel-body">
				{!! formGroup(2,'code','text','Category Code',old('code',($update['code'])?:$TCM->NewCode()),[]) !!}
				{!! formGroup(2,'name','text','Name',old('name',$update['name']),[]) !!}
				{!! formGroup(2,'description','textarea','Description',old('description',$update['description']),['style' => 'height:120px']) !!}
				{!! formGroup(2,'priority','select','Priority',old('priority',$update['description']),['selectOptions' => $TCM->priority_field_options]) !!}
				{!! formGroup(2,'available','select','Available',old('available',$update['available']),['selectOptions' => $TCM->available_field_options]) !!}
			</div>
			<div class="panel-footer clearfix">
				<input type="submit" name="submit" value="@if(isset($tcm)) Update @else Create @endif Category" class="btn btn-primary pull-right">
			</div>
		</div>
	</form></div>
</div>

@endsection