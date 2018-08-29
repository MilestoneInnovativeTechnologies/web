@extends("mail.page")
@include('BladeFunctions')
@section("content")
@php
$Data = (Request()->code) ? \App\Models\Mail::find(Request()->code) : null;

@endphp

<div class="content"><form method="post" enctype="multipart/form-data">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong class="panel-title">Send Email Message</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('mail.index'):url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="col-xs-5" style="padding-left: 0px">
				{!! formGroup(2,'code','text','Code',$Data->code,['labelStyle' => 'text-align:left', 'attr' => 'readonly']) !!}
				{!! formGroup(2,'subject','text','Subject',$Data->subject,['labelStyle' => 'text-align:left', 'attr' => 'readonly']) !!}
				{!! formGroup(2,'to','text','To (Email)','',['labelStyle' => 'text-align:left;']) !!}
				<div class="col-xs-1 to_button" style="padding-left: 0px;"><a href="javascript:AddEmailToReceipt()" class="btn btn-default pull-right"><div class="glyphicon glyphicon-play"></div></a></div>
				<div class="panel panel-default filter_customer">
					<div class="panel-heading"><span class="panel-title" style="font-weight: 100">Filter Customer</span><a href="javascript:TogglePanelView('filter_customer')" title="" class="btn btn-default btn-xs pull-right toggle_anchor"><span class="glyphicon glyphicon-plus"></span> </a>
					</div>
					<div class="panel-body" style="display: none">
						<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'distributor','text','Distributor','All',['labelStyle' => 'text-align:left']) !!}</div>
						<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'dealer','text','Dealer','All',['labelStyle' => 'text-align:left']) !!}</div>
						<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'product','select','Product',['labelStyle' => 'text-align:left', 'selectOptions' => array_merge(['' => 'All'],\App\Models\Product::pluck('name','code')->toArray())]) !!}</div>
						<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'edition','select','Edition',['labelStyle' => 'text-align:left', 'selectOptions' => array_merge(['' => 'All'],\App\Models\Edition::pluck('name','code')->toArray())]) !!}</div>
						<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'country','text','Country','All',['labelStyle' => 'text-align:left']) !!}</div>
						<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'customer','text','Customer',['labelStyle' => 'text-align:left']) !!}</div>
					</div>
					<div class="panel-footer clearfix"><a href="javascript:ReloadCustomers()" class="btn btn-sm btn-default pull-right">Reload Customers</a></div>
				</div>
				<div class="panel panel-default filter_distributor">
					<div class="panel-heading"><span class="panel-title" style="font-weight: 100">Filter Distributors</span><a href="javascript:TogglePanelView('filter_distributor')" title="" class="btn btn-default btn-xs pull-right toggle_anchor"><span class="glyphicon glyphicon-plus"></span> </a>
					</div>
					<div class="panel-body" style="display: none">
						<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'d_product','select','Product',['labelStyle' => 'text-align:left', 'selectOptions' => array_merge(['' => 'All'],\App\Models\Product::pluck('name','code')->toArray())]) !!}</div>
						<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'d_edition','select','Edition',['labelStyle' => 'text-align:left', 'selectOptions' => array_merge(['' => 'All'],\App\Models\Edition::pluck('name','code')->toArray())]) !!}</div>
						<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'d_country','text','Country','All',['labelStyle' => 'text-align:left']) !!}</div>
						<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'distributor','text','Distributor',['labelStyle' => 'text-align:left']) !!}</div>
					</div>
					<div class="panel-footer clearfix"><a href="javascript:ReloadDistributors()" class="btn btn-sm btn-default pull-right">Reload Distributors</a></div>
				</div>
			</div>
			<div class="col-xs-7" style="border-left: 1px solid #DDD;">
				<div class="table-responsive"><table class="table table-bordered table-striped table-condensed partners"><thead><tr><th colspan="2" width="66%">Select Partners</th><th colspan="1"><a href="javascript:InvertSelection()" class="pull-right">Invert Selection</a></th></tr></thead><tbody>

				</tbody></table></div>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="panel-footer clearfix">
			<input type="submit" name="submit" value="Proceed" class="btn btn-primary pull-right">
		</div>
	</div>
</form></div>

@endsection
@push('js')

<script type="text/javascript">
var _AllDistributors = {!! \App\Models\Distributor::all()->map(function($item){ return ['label' => $item->name, 'value' => $item->code]; })->toJson() !!}; _AllDistributors.unshift({label:'All',value:''});
var _AllDealers = {!! \App\Models\Dealer::all()->map(function($item){ return ['label' => $item->name, 'value' => $item->code]; })->toJson() !!}; _AllDealers.unshift({label:'All',value:''});
var _AllCountries = {!! \App\Models\PartnerCountries::all()->mapWithKeys(function($item){ return [$item->Country->id => ['label' => $item->Country->name, 'value' => $item->Country->id]]; })->values()->toJson() !!}; _AllCountries.unshift({label:'All',value:''});
var CustomerFilterData = {}; var DistributorFilterData = {};
function TogglePanelView(panel){
	AR = {'glyphicon-minus':'glyphicon-plus','glyphicon-plus':'glyphicon-minus'}
	$('.panel.'+panel+' .panel-body').slideToggle();
	$.each(AR,function(h,a){ if($('.panel.'+panel+' a.toggle_anchor span').hasClass(h)) { $('.panel.'+panel+' a.toggle_anchor span').removeClass(h).addClass(a); return false; } })
}
function AFD(N,V){
	V = $.trim(V);
	if(V == 'All' || !V) delete CustomerFilterData[N];
	else CustomerFilterData[N] = V;
}
function AFD2(N,V){
	V = $.trim(V);
	if(V == 'All' || !V) delete DistributorFilterData[N];
	else DistributorFilterData[N] = V;
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
$(function(){
	$('[name="to"]').parent().removeClass('col-xs-9').addClass('col-xs-8').parent().append($('.to_button'));
	$('[name="product"]').on('change',function(){ AFD("product",this.value); });
	$('[name="edition"]').on('change',function(){ AFD("edition",this.value); });
	$('[name="d_product"]').on('change',function(){ AFD2("product",this.value); });
	$('[name="d_edition"]').on('change',function(){ AFD2("edition",this.value); });
	$('[name="customer"]').on('keyup',function(){ AFD("customer",this.value); });
	$('[name="distributor"]').autocomplete({
		minLength: 0, delay: 0, source: _AllDistributors,
		focus: function(event, ui){ $('[name="distributor"]').val(ui.item.label); AFD("distributor",ui.item.value); return false; },
		select: function(event, ui){ $('[name="distributor"]').val(ui.item.label); AFD("distributor",ui.item.value); return false; }
	})
	$('[name="dealer"]').autocomplete({
		minLength: 0, delay: 0, source: _AllDealers,
		focus: function(event, ui){ $('[name="dealer"]').val(ui.item.label); AFD("dealer",ui.item.value); return false; },
		select: function(event, ui){ $('[name="dealer"]').val(ui.item.label); AFD("dealer",ui.item.value); return false; }
	})
	$('[name="country"]').autocomplete({
		minLength: 0, delay: 0, source: _AllCountries,
		focus: function(event, ui){ $('[name="country"]').val(ui.item.label); AFD("country",ui.item.value); return false; },
		select: function(event, ui){ $('[name="country"]').val(ui.item.label); AFD("country",ui.item.value); return false; }
	})
	$('[name="d_country"]').autocomplete({
		minLength: 0, delay: 0, source: _AllCountries,
		focus: function(event, ui){ $('[name="d_country"]').val(ui.item.label); AFD2("country",ui.item.value); return false; },
		select: function(event, ui){ $('[name="d_country"]').val(ui.item.label); AFD2("country",ui.item.value); return false; }
	})
})
function ReloadCustomers(){
	FireAPI('api/v1/mail/get/dcs',function(D){
		tbd = getPartnersTBD().empty();
		if($.isEmptyObject(D)) return tbd.html($('<tr>').html($('<th>').attr('colspan',6).addClass('text-center').text('No data found!')));
		$.each(D,function(i,Obj){ ATPT(Obj.code,Obj.name); })
	},CustomerFilterData)
}
function ReloadDistributors(){
	FireAPI('api/v1/mail/get/dds',function(D){
		tbd = getPartnersTBD().empty();
		if($.isEmptyObject(D)) return tbd.html($('<tr>').html($('<th>').attr('colspan',6).addClass('text-center').text('No data found!')));
		$.each(D,function(i,Obj){ ATPT(Obj.code,Obj.name); })
	},DistributorFilterData)
}
function InvertSelection(){
	$('[name="r[]"]').trigger('click');
}
function AddEmailToReceipt(){
	to = $('[name="to"]');
	email = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(String(to.val()).toLowerCase());
	if(!email) return alert('Email is not valid');
	ATPT(to.val(),to.val(),true); to.val('');
}
</script>
@endpush