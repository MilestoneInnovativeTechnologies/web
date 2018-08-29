<div class="panel panel-default @if(isset($class) && is_array($class)){{ implode(' ',$class) }}@else{{ $class }}@endif">
	<div class="panel-heading"><div class="panel-title">{{ $title }}</div>@if(isset($limits) && is_array($limits))<select class="limit_control pull-right form-control" style="width: 40px; margin-top:-28px; padding:0px;" onChange="FillDashboardTable('{{ $name }}')"><option>{!! implode('</option><option>',$limits) !!}</option></select>@endif</div>
	<div class="panel-body"><div class="table-responsive"><table class="table table-@if(isset($type) && is_array($type)){{ implode(' table-',$type) }}@else{{ $type }}@endif">
		<thead>{{ $slot }}</thead><tbody></tbody>
	</table></div>
	</div>
</div>
@push('js')
<script type="text/javascript">
$(function(){
	if(typeof(_Panels) == 'undefined') window['_Panels'] = {};
	if(typeof(_PanelTables) == 'undefined') window['_PanelTables'] = {};
	if(typeof(_DataURI) == 'undefined') window['_DataURI'] = {};
	if(typeof _DataFilter == 'undefined') _DataFilter = {};
	_Panels['{{ $name }}'] = '.{{ implode('.',(array) $class) }}';
	@if(isset($heads) && is_array($heads))_PanelTables['{{ $name }}'] = {!! json_encode($heads) !!};@endif
	@if(isset($data))_DataURI['{{ $name }}'] = '{{ $data }}';@endif
	@if(isset($data_filter))_DataFilter['{{ $name }}'] = '{{ $data_filter }}';@endif
})
</script>
@if(isset($js))<script type="text/javascript" src="{{ $js }}"></script>@endif

@endpush