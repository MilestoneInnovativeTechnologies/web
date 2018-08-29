<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'partner_distributor','text','Distributor','All',['labelStyle' => 'text-align:left']) !!}</div>
<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'partner_dealer','text','Dealer','All',['labelStyle' => 'text-align:left']) !!}</div>
<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'partner_product','select','Product',['labelStyle' => 'text-align:left', 'selectOptions' => array_merge(['' => 'All'],\App\Models\Product::pluck('name','code')->toArray())]) !!}</div>
<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'partner_edition','select','Edition',['labelStyle' => 'text-align:left', 'selectOptions' => array_merge(['' => 'All'],\App\Models\Edition::pluck('name','code')->toArray())]) !!}</div>
<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'partner_country','text','Country','All',['labelStyle' => 'text-align:left']) !!}</div>
<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'partner_partner','text','Partner',['labelStyle' => 'text-align:left']) !!}</div>
@push('js')
<script type="text/javascript">
var _PartnerFilterCountries = {!! \App\Models\PartnerCountries::all()->mapWithKeys(function($item){ return [$item->Country->id => ['label' => $item->Country->name, 'value' => $item->Country->id]]; })->values()->toJson() !!}; _PartnerFilterCountries.unshift({label:'All',value:''});
var _PartnerFilterDistributors = {!! \App\Models\Distributor::all()->map(function($item){ return ['label' => $item->name, 'value' => $item->code]; })->toJson() !!}; _PartnerFilterDistributors.unshift({label:'All',value:''});
var _PartnerFilterDealers = {!! \App\Models\Dealer::all()->map(function($item){ return ['label' => $item->name, 'value' => $item->code]; })->toJson() !!}; _PartnerFilterDealers.unshift({label:'All',value:''});
var _ReloadPartnerArgs = {};
function ReloadPartners(){
	FireAPI('api/v1/partner/get/dcs',function(D){
		PopulatePartners(D);
	},_ReloadPartnerArgs)
}
$(function(){
	$('[name="partner_partner"]').on('keyup',function(){ _ReloadPartnerArgs['partner'] = this.value; });
	$('[name="partner_product"]').on('change',function(){ _ReloadPartnerArgs['product'] = (this.value == 'All' || this.value == '') ? null : this.value; });
	$('[name="partner_edition"]').on('change',function(){ _ReloadPartnerArgs['edition'] = (this.value == 'All' || this.value == '') ? null : this.value; });
	$('[name="partner_country"]').autocomplete({
		minLength: 0, delay: 0, source: _PartnerFilterCountries,
		focus: function(event, ui){ $('[name="partner_country"]').val(ui.item.label); _ReloadPartnerArgs['country'] = (ui.item.value == 'All' || ui.item.value == '') ? null : ui.item.value; return false; },
		select: function(event, ui){ $('[name="partner_country"]').val(ui.item.label); _ReloadPartnerArgs['country'] = (ui.item.value == 'All' || ui.item.value == '') ? null : ui.item.value; return false; }
	})
	$('[name="partner_distributor"]').autocomplete({
		minLength: 0, delay: 0, source: _PartnerFilterDistributors,
		focus: function(event, ui){ $('[name="partner_distributor"]').val(ui.item.label); _ReloadPartnerArgs['distributor'] = (ui.item.value == 'All' || ui.item.value == '') ? null : ui.item.value; return false; },
		select: function(event, ui){ $('[name="partner_distributor"]').val(ui.item.label); _ReloadPartnerArgs['distributor'] = (ui.item.value == 'All' || ui.item.value == '') ? null : ui.item.value; return false; }
	})
	$('[name="partner_dealer"]').autocomplete({
		minLength: 0, delay: 0, source: _PartnerFilterDealers,
		focus: function(event, ui){ $('[name="partner_dealer"]').val(ui.item.label); _ReloadPartnerArgs['dealer'] = (ui.item.value == 'All' || ui.item.value == '') ? null : ui.item.value; return false; },
		select: function(event, ui){ $('[name="partner_dealer"]').val(ui.item.label); _ReloadPartnerArgs['dealer'] = (ui.item.value == 'All' || ui.item.value == '') ? null : ui.item.value; return false; }
	})
})
</script>
@endpush