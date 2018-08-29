@php
if(!function_exists('SecDiff')){
	function SecDiff($s){
		if(60 > $s) return $s . ' secs'; $d = 60;
		if(3600 > $s) { $min = floor($s/$d); $sec = $s%$d; return join(" ",[$min,'mins',$sec,'secs']); } $d = 3600;
		if(86400 > $s) { $hrs = floor($s/$d); $min = floor(($s%$d)/60); return join(" ",[$hrs,'hrs',$min,'mins']); } $d = 86400;
		if(2592000 > $s) { $dys = floor($s/$d); $hrs = floor(($s%$d)/3600); return join(" ",[$dys,'days',$hrs,'hrs']); }
		$d = 2592000;	$mn = floor($s/$d); $dys = floor(($s%$d)/86400); return join(" ",[$mn,'months',$dys,'days']);
	}
}
$AgentTasks = $Tasks->groupBy(function($item){
	return ($item->Responder) ? $item->Responder->responder : '-';
})->toArray();
$Activities = [];
foreach($AgentTasks as $Agent => $TskArray){
	if(!array_key_exists($Agent,$Activities)) $Activities[$Agent] = [];
	foreach($TskArray as $Tsk){
		$Status = $Tsk['cstatus']['status'];
		if(!array_key_exists($Status,$Activities[$Agent])) $Activities[$Agent][$Status] = [];
		$Text = $Tsk['title'];
		$Activities[$Agent][$Status][] = [$Tsk['id'],$Text,$Tsk['cstatus']['start_time']];
	}
}
if(!function_exists('TskToDesc')){
	function TskToDesc($Ary){
		$Desc = [];
		foreach($Ary as $DescArray) $Desc[] = '<a href="' . Route('task.panel',$DescArray[0]) . '" style="color: inherit">' . $DescArray[1] . '</a> - <small>(' . SecDiff(time()-$DescArray[2]) . ' ago)</small>';
		return $Desc;
	}
}
@endphp
<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Agent</th><th>Working</th><th>Holded</th></tr></thead><tbody>
	@forelse($Agents as $a)
	<tr>
		<th>{{ $loop->iteration }}</th>
		<td>
			{{ $a->name }}
		</td>
		<td>
			@if(array_key_exists($a->code,$Activities) && array_key_exists('WORKING',$Activities[$a->code]))
			<ol style="padding-left: 15px;"><li>{!! implode("</li><li>",TskToDesc($Activities[$a->code]['WORKING'])) !!}</li></ol>
			@else
			-NONE-
			@endif
		</td>
		<td>
			@if(array_key_exists($a->code,$Activities) && array_key_exists('HOLD',$Activities[$a->code]))
			<ol style="padding-left: 15px;"><li>{!! implode("</li><li>",TskToDesc($Activities[$a->code]['HOLD'])) !!}</li></ol>
			@else
			-NONE-
			@endif
		</td>
	@empty
	<tr><th colspan="4" style="text-align: center">No records found!</th></tr>
	@endforelse
</tbody></table></div>
