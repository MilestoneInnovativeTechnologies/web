@extends("gu.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="col col-md-8 col-md-offset-2"><form method="post">{{ csrf_field() }}
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Create General Upload Form</strong>{!! PanelHeadBackButton((url()->previous() == url()->current())?Route('gu.index'):url()->previous()) !!}</div>
			<div class="panel-body">
				{!! formGroup(2,'name','text','Name',old('name')) !!}
				{!! formGroup(2,'description','textarea','Description',old('description')) !!}
				{!! formGroup(2,'customer','text','Customer',old('customer'),['attr' => 'placeholder="customer code if any"']) !!}
				{!! formGroup(2,'overwrite','select','Overwritable',old('overwrite'),['selectOptions' => ['N' => 'No', 'Y' => 'Yes']]) !!}
			</div>
			<div class="panel-footer clearfix">
				<input type="submit" name="submit" value="Save Form" class="btn btn-primary pull-right">
			</div>
		</div>
	</form></div>
</div>

@endsection
@push('js')
<script type="text/javascript">
$(function(){
	$('[name="customer"]').autocomplete({
		minLength: 0,
		source: '/api/v1/gu/get/sc',
		select: function(event, ui){ $('[name="customer"]').val(ui.item.code); return false; },
		focus: function(event, ui){ $('[name="customer"]').val(ui.item.code); return false; }
	}).autocomplete( "instance" )._renderItem = function(ul, item) {
      return $( "<li>" ).appendTo( ul ).append( "<div>" + item.name + "</div>" );
    };
})
</script>
@endpush