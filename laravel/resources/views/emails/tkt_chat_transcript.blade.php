@extends('emails.layout')
@section("content")

			<p>As per the request received, we are mailing you the copy of Chat Transcript related to the Ticket <strong>{{ $Ticket->title }}</strong> ({{ $Ticket->code }})</p>
			<p><strong>Chat Transcript</strong></p>
			@component('emails.specialbox')
				<table cellpadding="3" cellspacing="3"><tbody style="font-family: Segoe UI, Arial; font-size: 12px;">
				@php $C = $Ticket->Conversations @endphp
				@foreach($C as $c)
				@continue($c->type == 'INFO')
				<tr><td nowrap valign="top">{{ $c->User->name }}<br><small><em>{{ date('d/m h:i a',strtotime($c->created_at)) }}</em></small></td><th valign="top">:</th><td valign="top">{!! ($c->type == 'CHAT') ? $c->content : ChatContent($c->type,$c->content,$c->ticket, $c->id) !!}</td></tr>
				@endforeach
				</tbody></table>
			@endcomponent

@endsection
@php
function ChatContent($type, $content, $tkt, $id){
	$C = json_decode($content,true);
	if($type == 'LINK') return LinkContent($C['name'],$C['description'],$C['link']);
	if($type == 'FILE') return FileContent($C,$tkt, $id);
}
function LinkContent($N,$D,$L){
	$C = []; $C[] = $N;
	$C[] = '<small>'.nl2br($D).'</small>';
	$C[] = '<a href="'.$L.'">Browse Link</a>';
	return implode('<br>',$C);
}
function FileContent($N,$T,$I){
	$C = []; $Mime = $N['mime'];
	$C[] = (mb_stripos($Mime,'image') === false) ? $N['name'] : '<img style="max-width: 80%;" src="'.Route('ticket.conversation.image',$N['file']).'">';
	$C[] = '<a href="' . Route("ticket.uploadedfile.download",["tkt" => $T, "id" => $I]) . '">Download</a>';
	return implode('<br>',$C);
}
@endphp