@extends("company.page")
@include('BladeFunctions')
@section("content")


<div class="content"><form method="post" enctype="multipart/form-data">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><div class="panel-title">Compose E-Mail</div></div>
		<div class="panel-body">
			<div class="col-xs-6">{!! formGroup(1,'code','text','Unique Code','',['labelStyle' => 'text-align:left']) !!}</div>
			<div class="col-xs-6">{!! formGroup(1,'subject','text','Subject','',['labelStyle' => 'text-align:left']) !!}</div>
			<div class="clearfix"></div>
			<div class="col-xs-12">{!! formGroup(1,'body','textarea','Email Body','',['attr' => 'id="editor" style="height:360px"']) !!}</div>
		</div>
		<div class="panel-footer clearfix">
			<input type="submit" name="submit" value="Proceed" class="btn btn-primary pull-right">
		</div>
	</div>
</form></div>

@endsection
@push('js')
<script type="text/javascript" src="js/ckeditor.js"></script>
<script>
$(function(){
	ClassicEditor.create( document.querySelector( '#editor' ) ).catch( error => { console.error( error ) } );
})
</script>
@endpush