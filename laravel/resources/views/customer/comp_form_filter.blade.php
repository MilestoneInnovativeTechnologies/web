<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'distributor','text','Distributor','All',['labelStyle' => 'text-align:left']) !!}</div>
<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'dealer','text','Dealer','All',['labelStyle' => 'text-align:left']) !!}</div>
<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'product','select','Product',['labelStyle' => 'text-align:left', 'selectOptions' => array_merge(['' => 'All'],\App\Models\Product::pluck('name','code')->toArray())]) !!}</div>
<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'edition','select','Edition',['labelStyle' => 'text-align:left', 'selectOptions' => array_merge(['' => 'All'],\App\Models\Edition::pluck('name','code')->toArray())]) !!}</div>
<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'country','text','Country','All',['labelStyle' => 'text-align:left']) !!}</div>
<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'customer','text','Customer',['labelStyle' => 'text-align:left']) !!}</div>
@push('js')
<script type="text/javascript">
var _CustomerFilterCountries = {!! \App\Models\PartnerCountries::all()->mapWithKeys(function($item){ return [$item->Country->id => ['label' => $item->Country->name, 'value' => $item->Country->id]]; })->values()->toJson() !!}; _CustomerFilterCountries.unshift({label:'All',value:''});
var _CustomerFilterDistributors = {!! \App\Models\Distributor::all()->map(function($item){ return ['label' => $item->name, 'value' => $item->code]; })->toJson() !!}; _CustomerFilterDistributors.unshift({label:'All',value:''});
var _CustomerFilterDealers = {!! \App\Models\Dealer::all()->map(function($item){ return ['label' => $item->name, 'value' => $item->code]; })->toJson() !!}; _CustomerFilterDealers.unshift({label:'All',value:''});
var _ReloadCustomersArgs = {};
function ReloadCustomers(){
	FireAPI('api/v1/customer/get/dcs',function(D){
		PopulatePartners(D);
	},_ReloadCustomersArgs)
}
$(function(){
	$('[name="customer"]').on('keyup',function(){ _ReloadCustomersArgs['customer'] = this.value; });
	$('[name="product"]').on('change',function(){ _ReloadCustomersArgs['product'] = (this.value == 'All' || this.value == '') ? null : this.value; });
	$('[name="edition"]').on('change',function(){ _ReloadCustomersArgs['edition'] = (this.value == 'All' || this.value == '') ? null : this.value; });
	$('[name="country"]').autocomplete({
		minLength: 0, delay: 0, source: _CustomerFilterCountries,
		focus: function(event, ui){ $('[name="country"]').val(ui.item.label); _ReloadCustomersArgs['country'] = (ui.item.value == 'All' || ui.item.value == '') ? null : ui.item.value; return false; },
		select: function(event, ui){ $('[name="country"]').val(ui.item.label); _ReloadCustomersArgs['country'] = (ui.item.value == 'All' || ui.item.value == '') ? null : ui.item.value; return false; }
	})
	$('[name="distributor"]').autocomplete({
		minLength: 0, delay: 0, source: _CustomerFilterDistributors,
		focus: function(event, ui){ $('[name="distributor"]').val(ui.item.label); _ReloadCustomersArgs['distributor'] = (ui.item.value == 'All' || ui.item.value == '') ? null : ui.item.value; return false; },
		select: function(event, ui){ $('[name="distributor"]').val(ui.item.label); _ReloadCustomersArgs['distributor'] = (ui.item.value == 'All' || ui.item.value == '') ? null : ui.item.value; return false; }
	})
	$('[name="dealer"]').autocomplete({
		minLength: 0, delay: 0, source: _CustomerFilterDealers,
		focus: function(event, ui){ $('[name="dealer"]').val(ui.item.label); _ReloadCustomersArgs['dealer'] = (ui.item.value == 'All' || ui.item.value == '') ? null : ui.item.value; return false; },
		select: function(event, ui){ $('[name="dealer"]').val(ui.item.label); _ReloadCustomersArgs['dealer'] = (ui.item.value == 'All' || ui.item.value == '') ? null : ui.item.value; return false; }
	})
})
</script>
@endpush