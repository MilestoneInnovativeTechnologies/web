@extends("tkt.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Chat Transcript - {{ $Data->code }} - {{ $Data->Cstatus->status }}</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('tkt.index',['tkt'=>$Data->code]):url()->previous()) !!}<span class="pull-right">&nbsp;</span>{!! PanelHeadButton('javascript:SendChatTrasnscript(\''.$Data->code.'\')','Send Chat Transcript','share-alt','info') !!}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col col-md-9">@if($Data->Conversations->isNotEmpty()) @php $cs = $Data->Conversations @endphp
					<div class="table-responsive">
						<table class="table table-striped"><tbody>
						@foreach($cs as $c)
							@if($c->type == 'INFO')
							<tr><th>{{ $loop->iteration }}</th><td>&nbsp;</td><td><code>{{ $c->content }}</code></td></tr>
							@else
							<tr><th>{{ $loop->iteration }}</th><td><strong>{{ $c->User->name }}</strong><br><small><em>{{ date('d/M/y - h:i a',strtotime($c->created_at)) }}</em></small></td><td>{!! ChatContent($c->type, $c->content, $c->ticket, $c->id) !!}</td></tr>
							@endif
						@endforeach
						</tbody></table>
					</div>@else
					<div class="jumbotron text-center text-uppercase"><h4>no conversations yet</h4></div>@endif
				</div>
				<div class="col col-md-3">{!! GetActions($Data) !!}</div>
			</div>
		</div>
	</div>
</div>

@endsection
@php
function ChatContent($type, $content, $tkt, $id){
	if($type == 'CHAT') return $content;
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
	$C[] = (mb_stripos($Mime,'image') === false) ? $N['name'] : '<img style="max-width: 300px;" src="'.Route('ticket.conversation.image',$N['file']).'">';//'<div class="col-xs-12 clearfix" style="margin-bottom: 10px; height: 400px; background: url('.Route('ticket.conversation.image',$N['file']).') no-repeat top left; background-size: contain;"></div>';
	$C[] = '<a href="' . Route("ticket.uploadedfile.download",["tkt" => $T, "id" => $I]) . '">Download</a>';
	return implode('<br>',$C);
}
function GetActions($Obj){
	$avps = array_diff($Obj->available_actions,['transcript']);
	if(empty($avps)) return '';
	$TitleIcon = ['view' => ['View ticket in detail','list-alt'], 'edit' => ['Edit this ticket','edit'], 'delete' => ['Delete this ticket','remove'], 'entitle' => ['Entitle this ticket','pencil'],'reassign' => ['Assign to another Support Team','user'],'tasks' => ['Manage Tasks','tasks'], 'communicate' => ['Chat with Support Team','comment'],'reopen' => ['Reopen this Ticket','repeat'],'closure' => ['Proceed Ticket closure activities','paperclip'], 'complete' => ['Complete this ticket','ok'],'feedback' => ['Provide feedback about this ticket','check'],'recreate' => ['Recreate same ticket','duplicate'],'enquire' => ['Enquire with customer','headphones'],'close' => ['Close ticket','eye-close'],'req_complete' => ['Send customer, complete request mail','envelope'],'force_complete' => ['Forcibly make this ticket as completed','eject'],'transcript' => ['View chat transcript','italic']];
	return implode('',array_map(function($act)use($Obj,$TitleIcon){ if($act == 'view') return '';
		return '<a href="'.Route('tkt.'.$act,$Obj->code).'" class="btn btn-default" style="width: 100%; margin-bottom: 3px; padding:15px 0px;">'.$TitleIcon[$act][0].'</a>';
	 return glyLink(Route('tkt.'.$act,$Obj->code), $TitleIcon[$act][0], $TitleIcon[$act][1], ['class' => 'btn btn-link']);
	},$avps));
}
@endphp
@push('js')
<script type="text/javascript" src="js/send_chat_transcript.js"></script>
<script type="text/javascript">
function GetTicketCode(){ return '{{ $Data->code }}'; }
function GetUserCode(){ return '{{ Auth()->user()->partner }}'; }
</script>
@endpush