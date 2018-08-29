@extends("tsk.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>{{ $Task->title }}</strong>{!! PanelHeadBackButton(Route('tsk.index'),'Back to Tasks') !!}<span class="pull-right">&nbsp;</span>{!! PanelHeadButton('javascript:SendChatTrasnscript(\''.$Task->ticket.'\')','Send Chat Transcript','share-alt','info') !!}<span class="pull-right">&nbsp;</span>{!! PanelHeadButton(Route('tsk.hold',$Task->id),'Hold Task','pause','warning') !!}<span class="pull-right">&nbsp;</span>{!! PanelHeadButton(Route('tsk.close',$Task->id),'Done/Close Task','ok','success') !!}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-md-8">
					<div class="panel panel-default chat_window">
						<div class="panel-heading"><h4 class="panel-title">Chat with {{ $Task->Ticket->Customer->name }}</h4></div>
						<div class="panel-body" style="min-height: 100px">
							<div class="conv_content_holder clearfix">
							</div>
						</div>
						<div class="panel-footer clearfix">
							<div class="col-xs-10">
								<div class="conversation_types pull-left"><a href="javascript:AlterConvType()">
									<div class="con_type chat active"></div>
									<div class="con_type file"></div>
								</a></div>
								<div class="conversation_control pull-right">
									<textarea name="chat_text" class="form-control chat_textarea" autofocus></textarea>
									<div class="btn btn-default browse_file" onClick="BrowseFile()">Browse file</div>
								</div>
							</div>
							<div class="col-xs-2" style="padding-left: 0px;"><a href="javascript:SendChatText()" class="btn btn-info chat_send_button" style="height: 55px; padding-top:15px; width:100%;">Send</a></div>
						</div>
					</div>
				</div>
				<div class="col-md-4" style="padding-left: 0px;">
					<div class="clearfix"><div class="well col-xs-6 text-center task_well" data-start="{{ $Task->created_at }}" data-text="Task Since"><div class="text"></div><div class="start" style="display: none"></div></div><div class="well col-xs-6 text-center ticket_well" data-start="{{ $Task->Ticket->created_at }}" data-text="Ticket Since"><div class="text"></div><div class="start" style="display: none"></div></div></div>
					<div class="panel panel-default details_panel task">
						<div class="panel-heading">Task Details{!! PanelHeadButton('javascript:TogglePanelView(\'task\')','','plus','default','sm') !!}</div>
						<div class="panel-body"><div class="table-responsive"><table class="table table-striped table-condensed"><tbody>
							<tr><th>Title</th><td>{{ $Task->title }}</td></tr>
							<tr><th>Descriprion</th><td>{{ $Task->description }}</td></tr>
							<tr><th>Support Type</th><td>{{ $Task->support_type?$Task->Stype->name:'' }}</td></tr>
							<tr><th>Weightage</th><td>{{ $Task->weightage }}</td></tr>
						</tbody></table></div></div>
					</div>
					<div class="panel panel-default details_panel ticket">
						<div class="panel-heading">Ticket Details{!! PanelHeadButton('javascript:TogglePanelView(\'ticket\')','','plus','default','sm') !!}</div>
						<div class="panel-body"><div class="table-responsive"><table class="table table-striped table-condensed"><tbody>@php $Tkt = $Task->Ticket; @endphp
							<tr><th>Code</th><td>{{ $Tkt->code }}</td></tr>
							<tr><th>Title</th><td>{{ $Tkt->title }}</td></tr>
							<tr><th>Priority</th><td>{{ $Tkt->priority }}</td></tr>
							<tr><th>Type</th><td>{{ $Tkt->ticket_type?$Tkt->Type->name:'' }}</td></tr>
							<tr><th>Category</th><td>{{ $Tkt->category?GetCategoryBreadCrumb($Tkt->Category):'' }}</td></tr>
							<tr><th>Descriprion</th><td>{{ $Tkt->description }}</td></tr>
							<tr><th>Created By</th><td>{{ $Tkt->Createdby->name }}</td></tr>
						</tbody></table></div></div>
					</div>
					<div class="panel panel-default details_panel attachments">
						<div class="panel-heading">Ticket Attachments{!! PanelHeadButton('javascript:TogglePanelView(\'attachments\')','','plus','default','sm') !!}</div>
						<div class="panel-body"><div class="table-responsive"><table class="table table-striped table-condensed"><tbody>@php $aths = $Task->Ticket->Attachments; @endphp
							@forelse($aths as $ath)
							<tr><td>{{ $ath->name }}</td><td><a href="{{ Route('ticket.download.attachment',$ath->file) }}">Download</a></td></tr>
							@empty
							<tr><td>No attachments</td></tr>
							@endforelse
						</tbody></table></div></div>
					</div>
					<div class="panel panel-default details_panel customer">
						<div class="panel-heading">Customer Details{!! PanelHeadButton('javascript:TogglePanelView(\'customer\')','','plus','default','sm') !!}</div>
						<div class="panel-body"><div class="table-responsive"><table class="table table-striped table-condensed"><tbody>@php $Cst = $Tkt->Customer; @endphp
							<tr><th>Code</th><td>{{ $Cst->code }}</td></tr>
							<tr><th>Name</th><td>{{ $Cst->name }}</td></tr>
							<tr><th>Email</th><td>{{ CustomerEmail($Cst->Logins) }}</td></tr>
							<tr><th>Phone</th><td>{{ CustomerPhone($Cst->Details) }}</td></tr>
						</tbody></table></div></div>
					</div>
					<div class="panel panel-default details_panel backups">
						<div class="panel-heading">Customer Backups{!! PanelHeadButton('javascript:TogglePanelView(\'backups\')','','plus','default','sm') !!}</div>
						<div class="panel-body"><div class="table-responsive"><table class="table table-striped table-condensed"><tbody>@php $B = ($Cst->Backups)?:null; @endphp
							@if($B && $B = $B->first())
							<tr><th>Details</th><td>{{ $B->details }}</td></tr>
							<tr><th>Date</th><td>{{ date('d/M/y h:i a',strtotime($B->created_at)) }}</td></tr>
							<tr><th>Size</th><td>{!! GetReadableSize($B->size) !!}</td></tr>
							<tr><td colspan="2"><a href="{{ $B->download_link }}" class="btn btn-default pull-right">Download</a></td></tr>
							@else
							<tr><td colspan="2">No backups</td></tr>
							@endif
						</tbody></table></div></div>
					</div>
					<div class="panel panel-default details_panel product">
						<div class="panel-heading">Product{!! PanelHeadButton('javascript:TogglePanelView(\'product\')','','plus','default','sm') !!}</div>
						<div class="panel-body"><div class="table-responsive"><table class="table table-striped table-condensed"><tbody>
							<tr><th>Product</th><td>{{ $Tkt->Product->name }}</td></tr>
							<tr><th>Edition</th><td>{{ $Tkt->Edition->name }}</td></tr>@php $Reg = GetReg($Tkt->Customer->Register,$Tkt->seqno) @endphp
							<tr><th>Version</th><td>{{ $Reg['version'] }}</td></tr>
							<tr><th>Database</th><td>{{ $Reg['database'] }}</td></tr>
							@foreach($Packages as $Package)
							<tr><th style="vertical-align: middle">{{ $Package->Package->name }}<br><small>{{ $Package->version_numeric }}</small></th>
								<td>
									<a href="javascript:SendDownloadLinkByChat('{{ $Tkt->product }}','{{ $Tkt->edition }}','{{ $Package->Package->code }}','{{ $Package->Package->type }}')" class="btn btn-sm btn-info" style="width: 100%; margin-bottom: 2px;">Send download link by chat</a>
									<a href="javascript:SendDownloadLinkByMail('{{ $Tkt->product }}','{{ $Tkt->edition }}','{{ $Package->Package->code }}','{{ $Tkt->customer }}')" class="btn btn-sm btn-info" style="width: 100%">Send download link by mail</a>
								</td>
							</tr>
							@endforeach
						</tbody></table></div></div>
					</div>
					<div class="panel panel-default details_panel customer_print_objects">@php $Pbs = $Tkt->Pobjects; @endphp
						<div class="panel-heading">Print Objects{!! PanelHeadButton('javascript:TogglePanelView(\'customer_print_objects\')','','plus','default','sm') !!}<span class="pull-right">&nbsp;</span>{!! PanelHeadButton('javascript:AddNewPrintObject()','Add Print Object','plus','info','sm') !!}</div>
						<div class="panel-body"><div class="table-responsive"><table class="table table-striped table-condensed"><thead><tr><th>Function</th><th>User</th><th>Action</th></tr></thead>
							<tbody style="font-size: 12px;">@if($Pbs->isNotEmpty()) @foreach($Pbs as $Pb) @continue($Pb->reg_seq != $Tkt->seqno)
								<tr id="customer_printobject_{{ $Pb->code }}" data-fncode="{{ $Pb->function_code }}"><td>{{ $Pb->function_code }}<br>{{ $Pb->function_name }}</td><td>{{ $Pb->User->name }}<br>{{ date('D d/m h:i A',$Pb->time) }}</td><td><a href="javascript:MailPrintObject('{{ $Pb->code }}')" class="btn" style="padding: 5px" title="Send Print Object download link by Mail"><span class="glyphicon glyphicon-envelope"></span></a><a href="javascript:ChatLinkPrintObject('{{ $Pb->code }}')" class="btn" style="padding: 5px" title="Send Print Object download link by Chat"><span class="glyphicon glyphicon-transfer"></span></a><a href="javascript:HistoryPrintObject('{{ $Pb->code }}','{{ $Pb->function_name }}','{{ $Pb->function_code }}')" class="btn" style="padding: 5px" title="View earlier versions"><span class="glyphicon glyphicon-list-alt"></span></a></td></tr>
							@endforeach @endif</tbody>
						</table></div></div>
					</div>
					<div class="panel panel-default details_panel customer_connections">@php $Cns = $Tkt->Connections; @endphp
						<div class="panel-heading">Customer Connections{!! PanelHeadButton('javascript:TogglePanelView(\'customer_connections\')','','plus','default','sm') !!}<span class="pull-right">&nbsp;</span>{!! PanelHeadButton('javascript:AddNewConnection()','Add One','plus','info','sm') !!}</div>
						<div class="panel-body"><div class="table-responsive"><table class="table table-striped table-condensed"><thead><tr><th>App</th><th>Login</th><th>Secret</th><th>Action</th></tr></thead>
							<tbody style="font-size: 12px;">@if($Cns->isNotEmpty()) @foreach($Cns as $Cn)
								<tr id="customer_connection_{{ $Cn->id }}"><td>{{ $Cn->appname }}</td><td>{{ $Cn->login }}</td><td>{{ $Cn->secret }}</td><td><a href="javascript:ViewConnection('{{ $Cn->id }}','{{ $Cn->appname }}','{{ $Cn->login }}','{{ $Cn->secret }}','{{ $Cn->remarks }}')" title="View in Detail"><span class="glyphicon glyphicon-list-alt"></span></a> &nbsp; <a href="javascript:DeleteConnection('{{ $Cn->id }}')" title="Delete this connection"><span class="glyphicon glyphicon-remove"></span></a></td></tr>
							@endforeach @endif</tbody>
						</table></div></div>
					</div>
					<div class="panel panel-default details_panel customer_cookies">@php $Cks = $Tkt->Cookies; @endphp
						<div class="panel-heading">Customer Cookies{!! PanelHeadButton('javascript:TogglePanelView(\'customer_cookies\')','','plus','default','sm') !!}<span class="pull-right">&nbsp;</span>{!! PanelHeadButton('javascript:AddNewCookie()','Add One','plus','info','sm') !!}</div>
						<div class="panel-body"><div class="table-responsive"><table class="table table-striped table-condensed"><thead><tr><th>Name</th><th>Value</th><th>Delete</th></tr></thead>
							<tbody style="font-size: 12px;">@if($Cks->isNotEmpty()) @foreach($Cks as $Ck)
								<tr id="customer_cookie_{{ $Ck->id }}"><td>{{ $Ck->name }}</td><td>{{ $Ck->value }}</td><td><a href="javascript:DeleteCookie('{{ $Ck->id }}')" title="Delete this cookie"><span class="glyphicon glyphicon-remove"></span></a></td></tr>
							@endforeach @endif</tbody>
						</table></div></div>
					</div>
					<div class="panel panel-default details_panel customer_uploads">@php $Ups = $Tkt->Customer->Uploads; @endphp
						<div class="panel-heading">Customer Uploads{!! PanelHeadButton('javascript:TogglePanelView(\'customer_uploads\')','','plus','default','sm') !!}<span class="pull-right">&nbsp;</span>{!! PanelHeadButton('javascript:AddNewUploadForm()','Add New Form','plus','info','sm') !!}</div>
						<div class="panel-body"><div class="table-responsive"><table class="table table-striped table-condensed"><thead><tr><th>Name</th><th>File</th><th>Actions</th></tr></thead>
							<tbody style="font-size: 12px;">@if($Ups->isNotEmpty()) @foreach($Ups as $Up)
								<tr id="customer_uploads_{{ $Up->code }}"><td>{{ $Up->name }}</td><td>{{ ($Up->file)?'Yes':'No' }}</td><td>-</td></tr>
							@endforeach @endif</tbody>
						</table></div></div>
					</div>
					<div class="panel panel-default details_panel tools">@php $Tls = \App\Models\ThirdPartyApplication::all(); @endphp
						<div class="panel-heading">Tools{!! PanelHeadButton('javascript:TogglePanelView(\'tools\')','','plus','default','sm') !!}<span class="pull-right">&nbsp;</span></div>
						<div class="panel-body"><div class="table-responsive"><table class="table table-striped table-condensed"><thead><tr><th>Name</th><th>Public</th><th>Actions</th></tr></thead>
							<tbody style="font-size: 12px;">@if($Tls->isNotEmpty()) @foreach($Tls as $Tl)
								<tr id="third_party_tools_{{ $Tl->code }}"><td>{{ $Tl->name }}</td><td>{{ $Tl->public }} @if($Tl->public == 'No') <input class="form-control pull-right" name="tpa_downloads_{{ $Tl->code }}" style="width:50px; height: 25px" placeholder="Downloads" value="1"> @endif</td><td><a href="javascript:TPTAction('{{ $Tl->code }}','mail')" title="Send download link by mail" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-envelope"></span></a>&nbsp;<a href="javascript:TPTAction('{{ $Tl->code }}','chat')" title="Send download link by chat" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-comment"></span></a></td></tr>
							@endforeach @endif</tbody>
						</table></div></div>
					</div>
					<div class="panel panel-default details_panel ticket_tasks">@php $Tsks = $Tkt->Tasks; @endphp
						<div class="panel-heading">All Tasks of this ticket{!! PanelHeadButton('javascript:TogglePanelView(\'ticket_tasks\')','','plus','default','sm') !!}</div>
						<div class="panel-body"><div class="table-responsive"><table class="table table-striped table-condensed"><thead><tr><th>Seq</th><th>Title</th><th>Status</th><th>Responder</th></tr></thead>
							<tbody style="font-size: 12px;">@if($Tsks->isNotEmpty()) @foreach($Tsks as $Tsk)
								<tr><td>{{ $Tsk->seqno }}</td><td>{{ $Tsk->title }}</td><td>{{ $Tsk->Cstatus->status }}</td><td>{{ $Tsk->Responder?$Tsk->Responder->Responder->name:'none' }}</td></tr>
							@endforeach @endif</tbody>
						</table></div></div>
					</div>
					<div class="panel panel-default details_panel task_status">@php $Tts = $Task->Status; @endphp
						<div class="panel-heading">Task Status flow{!! PanelHeadButton('javascript:TogglePanelView(\'task_status\')','','plus','default','sm') !!}</div>
						<div class="panel-body"><div class="table-responsive"><table class="table table-striped table-condensed"><thead><tr><th>No</th><th>Status</th><th>Time</th></tr></thead>
							<tbody>@if($Tts->isNotEmpty()) @foreach($Tts as $Tt)
								<tr><td>{{ $loop->iteration }}</td><td>{{ $Tt->status }}</td><td>{{ date("D d/m, h:i:s A",$Tt->start_time) }}</td></tr>
							@endforeach @endif</tbody>
						</table></div></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div style="display: none">
		<form enctype="multipart/form-data">
		<input type="hidden" name="task" value="{{ $Task->id }}">
		<input type="hidden" name="ticket" value="{{ $Task->ticket }}">
		<input type="hidden" name="user" value="{{ Request::user()->partner }}">
		<input name="chat_file" type="file" onChange="UploadFile()" /></form>
		<input type="hidden" name="customer" value="{{ $Task->Ticket->customer }}">
		<input type="hidden" name="product" value="{{ $Task->Ticket->product }}">
		<input type="hidden" name="edition" value="{{ $Task->Ticket->edition }}">
		<input type="hidden" name="reg_seq" value="{{ $Task->Ticket->seqno }}">
	</div>
</div>

@endsection
@push('css')
<link type="text/css" href="css/task_work.css" rel="stylesheet">
<link type="text/css" href="css/chat_window.css" rel="stylesheet">
@endpush
@push('js')
<script type="text/javascript" src="js/chat_window.js"></script>
<script type="text/javascript" src="js/chat_upload.js"></script>
<script type="text/javascript" src="js/chat_send.js"></script>
<script type="text/javascript" src="js/chat_populate.js"></script>
<script type="text/javascript" src="js/frequent_conv_check.js"></script>
<script type="text/javascript" src="js/chat_window_po.js"></script>
<script type="text/javascript" src="js/task_work.js"></script>
<script type="text/javascript" src="js/taskwork_general_upload.js"></script>
<script type="text/javascript" src="js/send_chat_transcript.js"></script>
<script type="text/javascript" src="js/chat_tpa.js"></script>
<script type="text/javascript">
@if($Ups->isNotEmpty())
	$(function(){
		PostUploadActions({!! $Ups !!});
	})
@endif
	var _ConvImageViewPath = '{{ Route("ticket.conversation.image","--PATH--") }}';
</script>
@endpush
@php
function GetCategoryBreadCrumb($Obj){
	$BCAry = [];
	if($Obj) $BCAry[] = $Obj->name;
	if($Obj->parent) array_unshift($BCAry,GetCategoryBreadCrumb($Obj->Parent));
	return implode(" &raquo; ", $BCAry);
}
function CustomerEmail($Obj){
	return $Obj->implode('email',',');
}
function CustomerPhone($Obj){
	return implode("",['+',$Obj->phonecode,'-',$Obj->phone]);
}
function GetReg($RegCol, $Seqno){
	if($RegCol->isEmpty()) return ['version' => null, 'database' => null];
	foreach($RegCol as $Reg){
		if($Reg->seqno == $Seqno)
			return $Reg->toArray();
	}
}
function GetReadableSize($size){
	$U = ['B','KB','MB','GB','TB']; $rs = $size; $C = 0;
	while($rs >= 1024){
		$rs = $rs/1024;
		$C++;
	}
	return join(" ",[round($rs,2),$U[$C]]);
}
@endphp