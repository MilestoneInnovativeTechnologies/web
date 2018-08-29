<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'supportagent_supportteam','text','Support Team',['labelStyle' => 'text-align:left']) !!}</div>
<div class="col-xs-6" style="padding: 0px">{!! formGroup(1,'supportagent_supportagent','text','Agent','',['labelStyle' => 'text-align:left']) !!}</div>
@push('js')
<script type="text/javascript">
var _SupportAgentFilterTeams = {!! \App\Models\SupportTeam::all()->map(function($item){ return ['label' => $item->name, 'value' => $item->code]; })->toJson() !!}; _SupportAgentFilterTeams.unshift({label:'All',value:''});
var _ReloadSupportAgentArgs = {};
function ReloadSupportAgents(){
	FireAPI('api/v1/tsa/get/tds',function(D){
		PopulatePartners(D);
	},_ReloadSupportAgentArgs)
}
$(function(){
	$('[name="supportagent_supportagent"]').on('keyup',function(){ _ReloadSupportAgentArgs['supportagent'] = this.value; });
	$('[name="supportagent_supportteam"]').autocomplete({
		minLength: 0, delay: 0, source: _SupportAgentFilterTeams,
		focus: function(event, ui){ $('[name="supportagent_supportteam"]').val(ui.item.label); _ReloadSupportAgentArgs['team'] = (ui.item.value == 'All' || ui.item.value == '') ? null : ui.item.value; return false; },
		select: function(event, ui){ $('[name="supportagent_supportteam"]').val(ui.item.label); _ReloadSupportAgentArgs['team'] = (ui.item.value == 'All' || ui.item.value == '') ? null : ui.item.value; return false; }
	})
})
</script>
@endpush