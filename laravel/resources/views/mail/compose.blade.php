@extends("mail.page")
@include('BladeFunctions')
@section("content")
@php
$code = Request()->code;
$Data = ($code) ? \App\Models\Mail::find($code) : null;
@endphp

<div class="content"><form method="post" enctype="multipart/form-data">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong class="panel-title">Compose E-Mail</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('mail.index'):url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="col-xs-9">{!! formGroup(1,'subject','text','Subject',old('subject',($Data)?$Data->subject:''),['labelStyle' => 'text-align:left']) !!}</div>
			<div class="col-xs-3">{!! formGroup(1,'code','text','Unique Code',old('compose',($Data)?$Data->code:(new \App\Models\Mail)->NewCode()),['labelStyle' => 'text-align:left']) !!}</div>
			<div class="clearfix"></div>
			<div class="col-xs-12">{!! formGroup(1,'body','textarea','Email Body',old('body',($Data)?$Data->body:''),['attr' => 'id="editor"']) !!}</div>
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