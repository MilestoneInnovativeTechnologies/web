@forelse($Data as $N)
<div class="panel panel-info not_{{ $N->code }} notification_panel" data-not="{{ $N->code }}">
	<div class="panel-heading">{{ $N->title }}<span class="pull-right">{{ date('d/M/y', strtotime($N->date)) }}</span></div>
	<div class="panel-body" style="height: 60px; padding-bottom: 0px;">{!! $N->description_short !!}</div>
	<div class="panel-body clearfix" style="padding: 0px 15px">{!! $N->serve_anchor() !!}</div>
</div>
@empty
@endforelse
@if($Data->isNotEmpty())
@push('js')
<script type="text/javascript" src="js/cookie.js"></script>
<script type="text/javascript" defer>
$(function(){
	$('.notification_panel').each(function(i,P){
		notify = $(P).attr('data-not'); if(Cookies.get('{{ $Data->first()->cookie_name_prefix }}{{ $Data->first()->_GETAUTHUSER()->partner }}.'+notify) == '{{ $Data->first()->cookie_value }}') $(P).remove();
	})
})
</script>
@endpush
@endif