@extends("notification.page")
@include('BladeFunctions')
@section("content")
@php
$Data = (isset($Data)) ? $Data : \App\Models\Notification::find(Request()->code);
@endphp

<div class="content">
	<div class="clearfix" style="margin-bottom: 2px">{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('notification.index'):url()->previous()) !!}</div>
	<div class="panel panel-default">
		<div class="panel-heading">{{ $Data->title }}<span class="pull-right">{{ date('D d/M/y',strtotime($Data->date)) }}</span></div>
		<div class="panel-body">
			{!! $Data->description !!}
		</div>
	</div>
</div>

@endsection
@push('js')
<script type="text/javascript" src="js/cookie.js"></script>
<script type="text/javascript">
$(function(){
	Cookies.set('{{ $Data->cookie_name_prefix }}{{ $Data->first()->_GETAUTHUSER()->partner }}.{{ $Data->code }}','{{ $Data->cookie_value }}',{ path: '/', expires: {{ intval(ceil((strtotime($Data->notify_to)-time())/86400))+1 }} })
})
</script>
@endpush