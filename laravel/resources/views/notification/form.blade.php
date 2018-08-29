@extends("notification.page")
@include('BladeFunctions')
@section("content")
@php
$Data = (Request()->code) ? \App\Models\Notification::find(Request()->code) : null;
@endphp

<div class="content"><form method="post" enctype="multipart/form-data">{{ csrf_field() }}
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
			<div class="panel-heading"><strong class="panel-title">Notification</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('notification.index'):url()->previous()) !!}</div>
			<div class="panel-body">
				<div class="clearfix">
					<div class="col-xs-4" style="padding-left:0px">{!! formGroup(2,'code','text','Code',old('code',($Data)?$Data->code:(new \App\Models\Notification)->NewCode()),['labelStyle' => 'text-align:left']) !!}</div>
					<div class="col-xs-8" style="padding-left:0px; padding-right:0px">{!! formGroup(2,'title','text','Title',old('title',($Data)?$Data->title:''),[]) !!}</div>
				</div>
				{!! formGroup(2,'description','textarea','Description',old('description',($Data)?$Data->description:''),['style' => 'height:120px', 'labelStyle' => 'text-align:left', 'attr' => 'id=\'description\'']) !!}
				{!! formGroup(2,'description_short','textarea','Short Description',old('description_short',($Data)?$Data->description_short:''),['labelStyle' => 'text-align:left', 'style' => 'height:110px']) !!}
				{!! formGroup(2,'date','text','Publish Date',old('date',($Data)?$Data->date:date('Y-m-d')),['labelStyle' => 'text-align:left']) !!}
				<div class="clearfix">
					<div class="col-xs-6" style="padding-left:0px">{!! formGroup(2,'notify_from','text','Notify from',old('notify_from',($Data)?$Data->notify_from:date('Y-m-d')),['labelStyle' => 'text-align:left', 'labelWidth' => 4]) !!}</div>
					<div class="col-xs-6" style="padding-left:0px; padding-right:0px">{!! formGroup(2,'notify_to','text','Notify Till',old('notify_to',($Data)?$Data->notify_to:date('Y-m-d')),['labelWidth' => 4]) !!}</div>
				</div>
			</div>
			<div class="panel-footer clearfix">
				<input type="submit" name="submit" value="Submit" class="btn btn-primary pull-right">
			</div>
		</div>
	</div>
</form></div>

@endsection
@push('js')
<!--<script type="text/javascript" src="js/ckeditor.js"></script>-->
<script type="text/javascript" src="js/nicEdit.js"></script>
<script type="text/javascript">
var _serve_url = '{{ Route("notification.serve",old("code",($Data)?$Data->code:(new \App\Models\Notification)->NewCode())) }}';
$(function(){
	$('[name="code"]').on('change',function(){ change_serve_url(this.value) });
	//$('[name="description"]').on('change',function(){ update_short_desc(this.value); console.log('as') });
	$('[name="description_short"]').on('change',function(){ _sdnc = true; });
	//ClassicEditor.create( document.querySelector( '#description' ) ).catch( error => { console.error( error ) } );
	new nicEditor({fullPanel : true}).panelInstance('description');
	check_for_desc_change();
});
function check_for_desc_change(){
	if(_sdnc) return false;
	update_short_desc($('[name="description"]').prev().children().text());
	setTimeout(check_for_desc_change,3000);
}
function change_serve_url(code){
	_serve_url = '{{ Route("notification.serve",old("code","--code--")) }}'.replace('--code--',code);
	ds = $('[name="description_short"]'); dsv = ds.val();
}
_sdnc = false;
function update_short_desc(desc){
	if(_sdnc) return;
	if(desc.length <= 160) return SetShortDesc(desc);
	Text = desc.substr(0,160); More = '........'; Anch = MoreAnch();
	SetShortDesc(Text + More + Anch);
}
function MoreAnch(){
	return '';
	return $('<a>').attr({ 'class':'btn btn-link', 'href':_serve_url }).text('Read >>>')[0].outerHTML;
}
function SetShortDesc(T){
	$('[name="description_short"]').val(T);
}
</script>
<script type="text/javascript" src="js/datepicker.js"></script>
<script type="text/javascript">
	$(function(){
		$("[name='notify_from'],[name='notify_to'],[name='date']").datepicker({format:'yyyy-mm-dd',autoclose:true,defaultViewDate:'today'});
	})
</script>
@endpush
@push("css")
<link rel="stylesheet" type="text/css" href="css/datepicker.css">
@endpush