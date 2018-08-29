<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'distributor_product','select','Product',['labelStyle' => 'text-align:left', 'selectOptions' => array_merge(['' => 'All'],\App\Models\Product::pluck('name','code')->toArray())]) !!}</div>
<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'distributor_edition','select','Edition',['labelStyle' => 'text-align:left', 'selectOptions' => array_merge(['' => 'All'],\App\Models\Edition::pluck('name','code')->toArray())]) !!}</div>
<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'distributor_country','text','Country','All',['labelStyle' => 'text-align:left']) !!}</div>
<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'distributor_distributor','text','Distributor',['labelStyle' => 'text-align:left']) !!}</div>
@push('js')
<script type="text/javascript">
var _DistributorFilterCountries = {!! \App\Models\PartnerCountries::all()->mapWithKeys(function($item){ return [$item->Country->id => ['label' => $item->Country->name, 'value' => $item->Country->id]]; })->values()->toJson() !!}; _DistributorFilterCountries.unshift({label:'All',value:''});
var _ReloadDistributorArgs = {};
function ReloadDistributors(){
	FireAPI('api/v1/distributor/get/dds',function(D){
		PopulatePartners(D);
	},_ReloadDistributorArgs)
}
$(function(){
	$('[name="distributor_distributor"]').on('keyup',function(){ _ReloadDistributorArgs['distributor'] = this.value; });
	$('[name="distributor_product"]').on('change',function(){ _ReloadDistributorArgs['product'] = (this.value == 'All' || this.value == '') ? null : this.value; });
	$('[name="distributor_edition"]').on('change',function(){ _ReloadDistributorArgs['edition'] = (this.value == 'All' || this.value == '') ? null : this.value; });
	$('[name="distributor_country"]').autocomplete({
		minLength: 0, delay: 0, source: _DistributorFilterCountries,
		focus: function(event, ui){ $('[name="distributor_country"]').val(ui.item.label); _ReloadDistributorArgs['country'] = (ui.item.value == 'All' || ui.item.value == '') ? null : ui.item.value; return false; },
		select: function(event, ui){ $('[name="distributor_country"]').val(ui.item.label); _ReloadDistributorArgs['country'] = (ui.item.value == 'All' || ui.item.value == '') ? null : ui.item.value; return false; }
	})
})
</script>
@endpush