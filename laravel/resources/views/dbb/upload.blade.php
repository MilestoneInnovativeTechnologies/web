@extends("dbb.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="col-md-8 col-md-offset-2"><form enctype="multipart/form-data" method="post">{{ csrf_field() }}
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Upload new backup</strong>{!! PanelHeadBackButton((url()->previous() == url()->current()) ? Route('dbb.index') : url()->previous()) !!}</div>
			<div class="panel-body">
				@unless(session()->get('_rolename') == 'customer') {!! formGroup(2,'customer','text','Customer') !!} @endunless
				{!! formGroup(2,'details','textarea','Details') !!}
				{!! formGroup(2,'backup','file','Backup') !!}
				<code class="col-xs-9 col-xs-offset-3" style="margin-top: -15px">Compress sql file into zip and upload zip file.</code>
			</div>
			<div class="panel-footer clearfix">
				<input type="submit" name="submit" value="Upload" class="btn btn-primary pull-right">
			</div>
		</div>
	</form></div>
</div>

@endsection
@push('js')
@unless(session()->get('_rolename') == 'customer')
<script type="text/javascript">
$(function(){
	$('[name="customer"]').autocomplete({
		minLength: 0,
		source: '/api/v1/dbb/get/sc',
		select: function(event, ui){ $('[name="customer"]').val(ui.item.code); return false; },
		focus: function(event, ui){ $('[name="customer"]').val(ui.item.code); return false; }
	}).autocomplete( "instance" )._renderItem = function(ul, item) {
      return $( "<li>" ).appendTo( ul ).append( "<div>" + item.name + "</div>" );
    };
})
</script>
@endunless
@endpush