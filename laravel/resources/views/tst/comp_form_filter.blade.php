<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'supportteam_country','text','Country','All',['labelStyle' => 'text-align:left']) !!}</div>
<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'supportteam_distributor','text','Distributor','All',['labelStyle' => 'text-align:left']) !!}</div>
<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'supportteam_supportteam','text','Support Team',['labelStyle' => 'text-align:left']) !!}</div>
@push('js')
<script type="text/javascript">
var _SupportteamFilterCountries = {!! \App\Models\PartnerCountries::all()->mapWithKeys(function($item){ return [$item->Country->id => ['label' => $item->Country->name, 'value' => $item->Country->id]]; })->values()->toJson() !!}; _SupportteamFilterCountries.unshift({label:'All',value:''});
var _SupportteamFilterDistributors = {!! \App\Models\Distributor::all()->map(function($item){ return ['label' => $item->name, 'value' => $item->code]; })->toJson() !!}; _SupportteamFilterDistributors.unshift({label:'All',value:''});
var _ReloadSupportteamArgs = {};
function ReloadSupportteams(){
	FireAPI('api/v1/tst/get/tds',function(D){
		PopulatePartners(D);
	},_ReloadSupportteamArgs)
}
$(function(){
	$('[name="supportteam_supportteam"]').on('keyup',function(){ _ReloadSupportteamArgs['supportteam'] = this.value; });
	$('[name="supportteam_country"]').autocomplete({
		minLength: 0, delay: 0, source: _DistributorFilterCountries,
		focus: function(event, ui){ $('[name="supportteam_country"]').val(ui.item.label); _ReloadSupportteamArgs['country'] = (ui.item.value == 'All' || ui.item.value == '') ? null : ui.item.value; return false; },
		select: function(event, ui){ $('[name="supportteam_country"]').val(ui.item.label); _ReloadSupportteamArgs['country'] = (ui.item.value == 'All' || ui.item.value == '') ? null : ui.item.value; return false; }
	})
	$('[name="supportteam_distributor"]').autocomplete({
		minLength: 0, delay: 0, source: _SupportteamFilterDistributors,
		focus: function(event, ui){ $('[name="supportteam_distributor"]').val(ui.item.label); _ReloadSupportteamArgs['distributor'] = (ui.item.value == 'All' || ui.item.value == '') ? null : ui.item.value; return false; },
		select: function(event, ui){ $('[name="supportteam_distributor"]').val(ui.item.label); _ReloadSupportteamArgs['distributor'] = (ui.item.value == 'All' || ui.item.value == '') ? null : ui.item.value; return false; }
	})
})
</script>
@endpush