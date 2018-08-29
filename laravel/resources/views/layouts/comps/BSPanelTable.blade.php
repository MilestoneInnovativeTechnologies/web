<div class="panel panel-default @if(is_array($class)){{ implode(' ',$class) }}@else{{ $class }}@endif">
	<div class="panel-heading"><div class="panel-title">{{ $title }}</div></div>
	<div class="panel-body"><div class="table-responsive"><table class="table table-@if(is_array($type)){{ implode(' table-',$type) }}@else{{ $type }}@endif">
		@if($heads)<thead><tr><th>{!! implode('</th><th>',(array) $heads) !!}</th></tr></thead>@endif
		<tbody>
		
		</tbody>
		</table></div>
	</div>
</div>