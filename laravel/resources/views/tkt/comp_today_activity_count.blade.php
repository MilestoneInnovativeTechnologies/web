@php
if(!function_exists('TH')){
	function TH($C,$S,$U = null,$T = null,$D = null){
		$ArgArray = [$T,$S]; if($U) $ArgArray['user'] = $U; if(!is_null($D)) $ArgArray['today'] = 1;
		return '<a href="'. Route('tickets.list',$ArgArray) .'" target="_blank" style="color: inherit">'.$C.'</a>';
	}
}
@endphp
<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Agent</th><th>Opened</th><th>Closed</th><th>Pending</th><th>Inprogress</th><th>Hold</th><th>Total</th></tr></thead><tbody>
	@forelse($Data as $t)
	<tr class="text-center"><th>{{ $loop->iteration }}</th><th>{{ $t->name }}</th><td>{!! TH($t->opened,'opened',$t->code,$Team,1) !!}</td><td>{!! TH($t->closed,'closed',$t->code,$Team,1) !!}</td><td>{!! TH($t->pending,'OPENED',$t->code,$Team) !!}</td><td>{!! TH($t->inprogress,'INPROGRESS',$t->code,$Team) !!}</td><td>{!! TH($t->hold,'HOLD',$t->code,$Team) !!}</td><td style="font-weight:bold">{!! TH($t->total,'CURRENT',$t->code,$Team) !!}</td></tr>
	@if($loop->last)
	<tr class="text-center" style="font-weight: bold"><td>&nbsp;</td><th>Total</th><td>{!! TH($Data->sum->opened,'opened',null,$Team,1) !!}</td><td>{!! TH($Data->sum->closed,'closed',null,$Team,1) !!}</td><td>{!! TH($Data->sum->pending,'OPENED',null,$Team) !!}</td><td>{!! TH($Data->sum->inprogress,'INPROGRESS',null,$Team) !!}</td><td>{!! TH($Data->sum->hold,'HOLD',null,$Team) !!}</td><td style="font-weight: 900">{!! TH($Data->sum->total,'CURRENT',null,$Team) !!}</td></tr>
	@endif
	@empty
	<tr><th colspan="8" style="text-align: center">No records found!</th></tr>
	@endforelse
</tbody></table></div>
