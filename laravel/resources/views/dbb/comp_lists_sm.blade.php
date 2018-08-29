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
<div class="table-responsive"><table class="table table-bordered"><thead><tr>
		<th>No</th><th>File Details</th>@if(in_array(session()->get('_rolename'),['supportteam','supportagent','company']))<th>Actions</th>@endif
	</tr></thead><tbody>
	@forelse($Dbbs as $Dbb)
	<tr>
		<th>{{ $loop->iteration }}</th>
		<td>Date: {{ date('d/M h:i a',strtotime($Dbb->created_at)) }}<br>Size: {{ GetReadableSize($Dbb->size) }}<br>Ext: {{ $Dbb->format }}</td>
		@if(in_array(session()->get('_rolename'),['supportteam','supportagent','company']))<td>{!! glyLink($Dbb->download_link, 'Download this backup', 'download', ['class' => 'btn']) !!}</td>@endif
	</tr>
	@empty
	<tr><th colspan="5" style="text-align: center">No records found!</th></tr>
	@endforelse
</tbody></table></div>