<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Product</th><th>Added On</th><th>Registered On</th><th>Version</th>@if(isset($Supportteams))<th>Support Team</th>@endif</tr></thead><tbody>
	@forelse($Registrations as $Reg)
	<tr><th>{{ $loop->iteration }}</th><td>{{ Reg2ProductName($Reg) }}</td><td>{{ date('D d/M/Y',strtotime($Reg->created_at)) }}</td><td>@if($Reg->registered_on) {{ date('D d/M/Y',strtotime($Reg->registered_on)) }}<br><small>({{ $Reg->serialno }})</small><br><small>({{ $Reg->key }})</small> @else <span class="glyphicon glyphicon-remove"></span> @endif</td><td>{{ $Reg->version }}</td>@if(isset($Supportteams))<td>@if($Team = SupportTeamOfPE($Reg->product, $Reg->edition, $Supportteams)) <a href="{{ Route('supportteam.panel',$Team->code) }}" style="text-decoration: none; color: inherit">{{ $Team->name }}</a> @endif</td>@endif</tr>
	@empty
	<tr><th colspan="6" style="text-align: center">No records found!</th></tr>
	@endforelse
</tbody></table></div>
@php
function Reg2ProductName($Reg){
	return implode(' ',[$Reg->Product->name,$Reg->Edition->name,'Edition']);
}
function SupportTeamOfPE($P,$E,$C){
	if($C->isEmpty()) return null;
	foreach($C as $c){
		if($P == $c->Product->code && $E == $c->Edition->code) return $c->Team;
	}
}
@endphp