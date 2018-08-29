@php
if(!function_exists('GetReadableSize')){
	function GetReadableSize($size){
		$U = ['B','KB','MB','GB','TB']; $rs = $size; $C = 0;
		while($rs >= 1024){
			$rs = $rs/1024;
			$C++;
		}
		return join(" ",[round($rs,2),$U[$C]]);
	}
}
@endphp
<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Name</th><th>Details</th></tr></thead><tbody>
	@forelse($Forms as $Fm)
	<tr><th>{{ $loop->iteration }}</th><td>{{ $Fm->name }}@if($Fm->description)<br><small>({!! nl2br($Fm->description) !!})</small>@endif</td><td>@if($Fm->file) <em>File Exists</em> <a href="{{ $Fm->download }}">Download</a><br>Size: {{ GetReadableSize($Fm->size) }}<br>Time: {{ date('d/M/y',$Fm->time) }}<br>Overwrite: {{ (['Y' => 'YES', 'N' => 'NO'])[$Fm->overwrite] }} @else No @endif</td></tr>
	@empty
	<tr><th colspan="5" style="text-align: center">No records found!</th></tr>
	@endforelse
</tbody></table></div>