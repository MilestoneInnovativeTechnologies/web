<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'dealer_product','select','Product',['labelStyle' => 'text-align:left', 'selectOptions' => array_merge(['' => 'All'],\App\Models\Product::pluck('name','code')->toArray())]) !!}</div>
<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'dealer_edition','select','Edition',['labelStyle' => 'text-align:left', 'selectOptions' => array_merge(['' => 'All'],\App\Models\Edition::pluck('name','code')->toArray())]) !!}</div>
<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'dealer_country','text','Country','All',['labelStyle' => 'text-align:left']) !!}</div>
<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'dealer_distributor','text','Distributor','All',['labelStyle' => 'text-align:left']) !!}</div>
<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'dealer_dealer','text','Dealer',['labelStyle' => 'text-align:left']) !!}</div>
@push('js')
<script type="text/javascript">
var _DealerFilterCountries = {!! \App\Models\PartnerCountries::all()->mapWithKeys(function($item){ return [$item->Country->id => ['label' => $item->Country->name, 'value' => $item->Country->id]]; })->values()->toJson() !!}; _DealerFilterCountries.unshift({label:'All',value:''});
var _DealerFilterDistributors = {!! \App\Models\Distributor::all()->map(function($item){ return ['label' => $item->name, 'value' => $item->code]; })->toJson() !!}; _DealerFilterDistributors.unshift({label:'All',value:''});
var _ReloadDealerArgs = {};
function ReloadDealers(){
	FireAPI('api/v1/dealer/get/dds',function(D){
		PopulatePartners(D);
	},_ReloadDealerArgs)
}
$(function(){
	$('[name="dealer_dealer"]').on('keyup',function(){ _ReloadDealerArgs['dealer'] = this.value; });
	$('[name="dealer_product"]').on('change',function(){ _ReloadDealerArgs['product'] = (this.value == 'All' || this.value == '') ? null : this.value; });
	$('[name="dealer_edition"]').on('change',function(){ _ReloadDealerArgs['edition'] = (this.value == 'All' || this.value == '') ? null : this.value; });
	$('[name="dealer_country"]').autocomplete({
		minLength: 0, delay: 0, source: _DealerFilterCountries,
		focus: function(event, ui){ $('[name="dealer_country"]').val(ui.item.label); _ReloadDealerArgs['country'] = (ui.item.value == 'All' || ui.item.value == '') ? null : ui.item.value; return false; },
		select: function(event, ui){ $('[name="dealer_country"]').val(ui.item.label); _ReloadDealerArgs['country'] = (ui.item.value == 'All' || ui.item.value == '') ? null : ui.item.value; return false; }
	})
	$('[name="dealer"]').autocomplete({
		minLength: 0, delay: 0, source: _DealerFilterDistributors,
		focus: function(event, ui){ $('[name="dealer_distributor"]').val(ui.item.label); _ReloadDealerArgs['dealer'] = (ui.item.value == 'All' || ui.item.value == '') ? null : ui.item.value; return false; },
		select: function(event, ui){ $('[name="dealer_distributor"]').val(ui.item.label); _ReloadDealerArgs['dealer'] = (ui.item.value == 'All' || ui.item.value == '') ? null : ui.item.value; return false; }
	})
})
</script>
@endpush