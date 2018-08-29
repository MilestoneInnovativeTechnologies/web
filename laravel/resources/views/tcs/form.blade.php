@extends("tcs.page")
@include('BladeFunctions')
@section("content")
@php
$update = [];
$fields = ['code','name','description','type','spec'];
foreach($fields as $field){
	$update[$field] = isset($tcs) ? $tcs->$field : null;
}
$TCS = new \App\Models\TicketCategorySpecification;
@endphp

<div class="content">
	<div class="col col-md-8 col-md-offset-2"><form method="post">{{ csrf_field() }}
		<div class="panel panel-default">
			<div class="panel-heading"><strong>@if(isset($tcs)) Update @else Create New @endif Specification</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('tcs.index'):url()->previous()) !!}</div>
			<div class="panel-body">
				<div class="col-xs-6">{!! formGroup(2,'code','text','Code',old('code',($update['code'])?:$TCS->NewCode()),[]) !!}</div>
				<div class="col-xs-6">{!! formGroup(2,'type','select','Type',old('type',$update['type']),['selectOptions' => $TCS->type_field_options]) !!}</div>
				<div class="col-xs-12">{!! formGroup(2,'spec','select','Specification',old('spec',$update['spec']),['selectOptions' => $TCS->whereNull('spec')->pluck('name','code')->toArray(), 'attr' => 'disabled']) !!}</div>
				
				
				
				<div class="col-xs-12">{!! formGroup(2,'name','text','Name',old('name',$update['name']),[]) !!}</div>
				<div class="col-xs-12">{!! formGroup(2,'description','textarea','Description',old('description',$update['description']),['style' => 'height:120px']) !!}</div>
			</div>
			<div class="panel-footer clearfix">
				<input type="submit" name="submit" value="@if(isset($tcs)) Update @else Create @endif Specification" class="btn btn-primary pull-right">
			</div>
		</div>
	</form></div>
</div>

@endsection
@push('js')
<script type="text/javascript">
$(function(){
	$('div.form-group:eq(2)').css('display','none');
	$('[name="type"]').on("change",function(){
		$('[name="spec"]').prop('disabled',$(this).val() == 'SPEC');
		$('div.form-group:eq(2)').slideToggle(200);
	})
	if($('[name="type"]').val() == 'VALUE') $('[name="type"]').trigger('change');
})
</script>
@endpush
@push('css')
<style type="text/css">
	label { text-align: left !important; }
</style>
@endpush