@extends("notification.page")
@include('BladeFunctions')
@section("content")
@php
$Data = \App\Models\Notification::find(Request()->code)->load('Audience');
$str_TarOpts = DB::select(DB::raw('SHOW COLUMNS FROM notifications WHERE Field = "target"'))[0]->Type;
$str_TarTypOpts = DB::select(DB::raw('SHOW COLUMNS FROM notifications WHERE Field = "target_type"'))[0]->Type;
$Options = [];
eval(str_replace('enum','$Options[] = array',$str_TarOpts.';'.$str_TarTypOpts.';'));
//dd($Data->toArray());
@endphp

<div class="content"><form method="post" enctype="multipart/form-data">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>{{ $Data->title }}</strong> - {{ date('d/M/y',strtotime($Data->date)) }}<div class="pull-right">{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('notification.index'):url()->previous()) !!}</div></div>
		<div class="panel-body pull-left" style="width: 450px;">
			{!! formGroup(2, 'target', 'select', 'Select Target', $Data->target, ['selectOptions' => collect($Options[0])->mapWithKeys(function($item){ return [$item => ucwords($item)]; })->merge([''=>'NONE']), 'labelWidth' => 4]) !!}
			{!! formGroup(2, 'target_type', 'select', 'Target Type', $Data->target_type, ['selectOptions' => collect($Options[01])->mapWithKeys(function($item){ return [$item => ucwords($item)]; }), 'labelWidth' => 4]) !!}
			<hr>
			<div class="panel panel-default filter_customer filter_panel">
				<div class="panel-heading"><span class="panel-title" style="font-weight: 100">Filter Customer</span><a href="javascript:TogglePanelView('filter_customer')" title="" class="btn btn-default btn-xs pull-right toggle_anchor"><span class="glyphicon glyphicon-plus"></span> </a>
				</div>
				<div class="panel-body">
				@component('customer.comp_form_filter') @endcomponent
				</div>
				<div class="panel-footer clearfix"><a href="javascript:ReloadCustomers()" class="btn btn-sm btn-default pull-right">Reload Customers</a></div>
			</div>
			<div class="panel panel-default filter_distributor filter_panel">
				<div class="panel-heading"><span class="panel-title" style="font-weight: 100">Filter Distributor</span><a href="javascript:TogglePanelView('filter_customer')" title="" class="btn btn-default btn-xs pull-right toggle_anchor"><span class="glyphicon glyphicon-plus"></span> </a>
				</div>
				<div class="panel-body">
				@component('distributor.comp_form_filter') @endcomponent
				</div>
				<div class="panel-footer clearfix"><a href="javascript:ReloadDistributors()" class="btn btn-sm btn-default pull-right">Reload Distributors</a></div>
			</div>
			<div class="panel panel-default filter_dealer filter_panel">
				<div class="panel-heading"><span class="panel-title" style="font-weight: 100">Filter Dealer</span><a href="javascript:TogglePanelView('filter_customer')" title="" class="btn btn-default btn-xs pull-right toggle_anchor"><span class="glyphicon glyphicon-plus"></span> </a>
				</div>
				<div class="panel-body">
				@component('dealer.comp_form_filter') @endcomponent
				</div>
				<div class="panel-footer clearfix"><a href="javascript:ReloadDealers()" class="btn btn-sm btn-default pull-right">Reload Dealers</a></div>
			</div>
			<div class="panel panel-default filter_supportteam filter_panel">
				<div class="panel-heading"><span class="panel-title" style="font-weight: 100">Filter Dealer</span><a href="javascript:TogglePanelView('filter_customer')" title="" class="btn btn-default btn-xs pull-right toggle_anchor"><span class="glyphicon glyphicon-plus"></span> </a>
				</div>
				<div class="panel-body">
				@component('tst.comp_form_filter') @endcomponent
				</div>
				<div class="panel-footer clearfix"><a href="javascript:ReloadSupportteams()" class="btn btn-sm btn-default pull-right">Reload Support Teams</a></div>
			</div>
			<div class="panel panel-default filter_supportagent filter_panel">
				<div class="panel-heading"><span class="panel-title" style="font-weight: 100">Filter Agents</span><a href="javascript:TogglePanelView('filter_customer')" title="" class="btn btn-default btn-xs pull-right toggle_anchor"><span class="glyphicon glyphicon-plus"></span> </a>
				</div>
				<div class="panel-body">
				@component('tsa.comp_form_filter') @endcomponent
				</div>
				<div class="panel-footer clearfix"><a href="javascript:ReloadSupportAgents()" class="btn btn-sm btn-default pull-right">Reload Support Agents</a></div>
			</div>
		</div>
		<div class="panel-body" style="border-left: 1px solid #DDD;">
			<div class="table-responsive"><table class="table table-bordered table-striped table-condensed partners"><thead><tr><th colspan="2" width="66%" class="partners_select_title">Select Partners</th><th colspan="1"><a href="javascript:InvertSelection()" class="pull-right">Invert Selection</a></th></tr></thead><tbody>

			</tbody></table></div>
		</div>
		<div class="panel-footer clearfix">
			<input type="submit" name="submit" class="btn btn-primary pull-right" value="Update">
		</div>
	</div>
</form></div>

@endsection
@push('js')
<script type="text/javascript">
var _target = '{{ $Data->target }}';
var _target_type = '{{ $Data->target_type }}';
var FireAPI = (function(a,b,c) {
  var _OF_FireAPI = FireAPI;
		return function(a,b,c) {
				SetTitle('Loading....');
				return _OF_FireAPI(a,b,c);
		}
})();
$(function(){
	$('.filter_panel').css('display','none');
	$('[name="target"]').on('change',function(){ _target = this.value; show_filters(); })
	$('[name="target_type"]').on('change',function(){ _target_type = this.value; show_filters(); });
	show_filters();
})
function show_filters(target){
	if(!_target || _target_type == 'All' ) return hide_all_filters();
	$('.filter_panel').filter(':not(".filter_'+_target+'")').slideUp().end().filter('.filter_'+_target+'').slideDown();
}
function hide_all_filters(){
	$('.filter_panel').slideUp();
}
function InvertSelection(){
	$('[name="r[]"]').trigger('click');
}
function PopulatePartners(D){
	TitleCorrect();
	tbd = getPartnersTBD().empty();
	if($.isEmptyObject(D)) return tbd.html($('<tr>').html($('<th>').attr('colspan',6).addClass('text-center').text('No data found!')));
	$.each(D,function(i,Obj){ ATPT(Obj.code,Obj.name); })
}
function TitleCorrect(){
	TypeTitle = {'Except':'excluded', 'Only':'included'};
	if(_target_type == 'All') SetTitle('')
	else SetTitle('Select '+_target+'s, to get '+TypeTitle[_target_type]);
}
function SetTitle(Title){
	$('.partners_select_title').text(Title);
}
function getPartnersTBD(){
	return $('table.partners tbody');
}
function ATPT(V,L,S){
	C = GC(V,L); if(S) C.find('input').attr('checked',true)
	T = GNPTD();
	T.html(C);
}
function GC(V,L){
	return $('<label>').addClass('checkbox-inline').css({margin:'0px',fontSize:'12px'}).html([$('<input>').css({marginTop:'1px'}).attr({type:'checkbox',value:V,name:'r[]'}),(" "+L)]);
}
function GNPTD(){
	tbd = getPartnersTBD(); cols = 0; tbd.prev().find('tr:first').children().each(function(j,col){ cols += parseInt($(col).attr('colspan')) || 1 });
	LTR = tbd.find('tr:last'); if(LTR.length === 0 || LTR.children().length === cols) return $('<tr>').appendTo(tbd).html($('<td>')).find('td:last');
	return $('<td>').appendTo(LTR);
}
function TogglePanelView(panel){
	AR = {'glyphicon-minus':'glyphicon-plus','glyphicon-plus':'glyphicon-minus'}
	$('.panel.'+panel+' .panel-body').slideToggle();
	$.each(AR,function(h,a){ if($('.panel.'+panel+' a.toggle_anchor span').hasClass(h)) { $('.panel.'+panel+' a.toggle_anchor span').removeClass(h).addClass(a); return false; } })
}
@if($Data->Audience->isNotEmpty())
$(function(){
	TitleCorrect();
	$.each({!! $Data->Audience->mapWithKeys(function($item){ return [$item->code => $item->name]; }) !!},function(c,n){ ATPT(c,n,true); })
})
@endif
</script>
@endpush