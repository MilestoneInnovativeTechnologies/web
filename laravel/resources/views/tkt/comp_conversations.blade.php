<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>User &amp; Time</th><th>Content</th></tr></thead><tbody>
	@forelse($Conversations as $c)
	@if($c->type == 'INFO')
	<tr><th>{{ $loop->iteration }}</th><td colspan="2"><code>{{ $c->content }}</code></td></tr>
	@else
	<tr><th>{{ $loop->iteration }}</th><td nowrap>{{ $c->User->name }}<br><small><em>{{ date('d/m - h:i a',strtotime($c->created_at)) }}</em></small></td><td>{!! ChatContent($c->type, $c->content, $c->ticket, $c->id) !!}</td></tr>
	@endif
	@empty
	<tr><th colspan="4" style="text-align: center">No records found!</th></tr>
	@endforelse
</tbody></table></div>
@php
function ChatContent($type, $content, $tkt, $id){
	if($type == 'CHAT') return $content;
	$C = json_decode($content,true);
	if($type == 'LINK') return LinkContent($C['name'],$C['description'],$C['link']);
	if($type == 'FILE') return FileContent($C['name'],$tkt, $id);
}
function LinkContent($N,$D,$L){
	$C = []; $C[] = $N;
	$C[] = '<small><i>'.nl2br($D).'</i></small>';
	$C[] = '<a href="'.$L.'">Browse Link</a>';
	return implode('<br>',$C);
}
function FileContent($N,$T,$I){
	$C = []; $C[] = $N;
	$C[] = '<a href="' . Route("ticket.uploadedfile.download",["tkt" => $T, "id" => $I]) . '">Download</a>';
	return implode('<br>',$C);
}
@endphp