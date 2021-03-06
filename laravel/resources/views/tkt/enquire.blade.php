@extends("tkt.page")
@include('BladeFunctions')
@section("content")
@php
$Tkt = \App\Models\Ticket::whereCode(Request()->tkt)->with(['Customer' => function($Q){ $Q->with('Logins','Details'); },'Product','Edition','Team.Team' => function($Q){ $Q->with('Logins','Details'); },'Tasks','Status'])->first();
//dd($Tkt->toArray())
@endphp

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Enquire with {{ $Tkt->Customer->name }}</strong>{!! PanelHeadBackButton(Route('tkt.index'),'Back to Tickets') !!}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-md-8">
					<div class="panel panel-default chat_window">
						<div class="panel-heading"><h4 class="panel-title">Chat Window</h4></div>
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
				<div class="col-md-4" style="padding-left: 0px;">@if($Tkt->Cstatus->status == 'REOPENED')
					<div class="panel panel-default details_panel reopen_reason">
						<div class="panel-heading">Reopen Reason</div>
						<div class="panel-body">{{ $Tkt->Cstatus->status_text }}</div>
					</div>@endif
					<div class="panel panel-default details_panel ticket_tasks">@php $Tsks = $Tkt->Tasks; @endphp
						<div class="panel-heading">All Tasks of this ticket{!! PanelHeadButton('javascript:TogglePanelView(\'ticket_tasks\')','','plus','default','sm') !!}</div>
						<div class="panel-body" style="display: none"><div class="table-responsive"><table class="table table-striped table-condensed"><thead><tr><th>Seq</th><th>Title</th><th>Status</th><th>Responder</th></tr></thead>
							<tbody>@if($Tsks->isNotEmpty()) @foreach($Tsks as $Tsk)
								<tr><td><small>{{ $Tsk->seqno }}</small></td><td><small>{{ $Tsk->title }}</small></td><td><small>{{ $Tsk->Cstatus->status }}</small></td><td><small>{{ $Tsk->Responder?$Tsk->Responder->Responder->name:'none' }}</small></td></tr>
							@endforeach @endif</tbody>
						</table></div></div>
					</div>
					<div class="panel panel-default details_panel ticket_status">@php $Sts = $Tkt->Status; @endphp
						<div class="panel-heading">Ticket Status flow{!! PanelHeadButton('javascript:TogglePanelView(\'ticket_status\')','','plus','default','sm') !!}</div>
						<div class="panel-body" style="display: none"><div class="table-responsive"><table class="table table-striped table-condensed"><thead><tr><th>No</th><th>Status</th><th>Text</th><th>Time</th></tr></thead>
							<tbody>@if($Sts->isNotEmpty()) @foreach($Sts as $St)
								<tr><td><small>{{ $loop->iteration }}</small></td><td><small>{{ $St->status }}</small></td><td><small>{{ $St->status_text }}</small></td><td><small>{{ date('dS M, h:i A',$St->start_time) }}</small></td></tr>
							@endforeach @endif</tbody>
						</table></div></div>
					</div>
					<div class="panel panel-default details_panel ticket">
						<div class="panel-heading">Ticket Details{!! PanelHeadButton('javascript:TogglePanelView(\'ticket\')','','plus','default','sm') !!}</div>
						<div class="panel-body" style="display: none"><div class="table-responsive"><table class="table table-striped table-condensed"><tbody>
							<tr><th>Code</th><td>{{ $Tkt->code }}</td></tr>
							<tr><th>Title</th><td>{{ $Tkt->title }}</td></tr>
							<tr><th>Product</th><td>{{ $Tkt->Product->name }} {{ $Tkt->Edition->name }} Edition</td></tr>
							<tr><th>Priority</th><td>{{ $Tkt->priority }}</td></tr>
							<tr><th>Type</th><td>{{ $Tkt->ticket_type?$Tkt->Type->name:'' }}</td></tr>
							<tr><th>Category</th><td>{{ $Tkt->category?GetCategoryBreadCrumb($Tkt->Category):'' }}</td></tr>
							<tr><th>Descriprion</th><td>{{ $Tkt->description }}</td></tr>
							<tr><th>Created By</th><td>{{ $Tkt->Createdby->name }}, <small>On: {{(date('D d/m h:i A',strtotime($Tkt->created_at)))}}</small></td></tr>
						</tbody></table></div></div>
					</div>
					<div class="panel panel-default details_panel customer">
						<div class="panel-heading">Customer Details{!! PanelHeadButton('javascript:TogglePanelView(\'customer\')','','plus','default','sm') !!}</div>
						<div class="panel-body" style="display: none"><div class="table-responsive"><table class="table table-striped table-condensed"><tbody>@php $Cst = $Tkt->Customer @endphp
							<tr><th>Name</th><td>{{ $Cst->name }}</td></tr>
							<tr><th>Address</th><td>{!! Address($Cst->Details) !!}</td></tr>
							<tr><th>Email</th><td>{{ Email($Cst->Logins) }}</td></tr>
							<tr><th>Phone</th><td>{{ Phone($Cst->Details) }}</td></tr>
						</tbody></table></div></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div style="display: none">
		<form enctype="multipart/form-data">
		<input type="hidden" name="task" value="">
		<input type="hidden" name="ticket" value="{{ $Tkt->code }}">
		<input type="hidden" name="user" value="{{ Request::user()->partner }}">
		<input type="hidden" name="customer" value="{{ Request::user()->partner }}">
		<input name="chat_file" type="file" onChange="UploadFile()" /></form>
		<input type="hidden" name="team" value="{{ $Tkt->Team->Team->code }}">
		<input type="hidden" name="product" value="{{ $Tkt->Product->code }}">
		<input type="hidden" name="edition" value="{{ $Tkt->Edition->code }}">
	</div>
</div>

@endsection
@push('css')
<link type="text/css" href="css/chat_window.css" rel="stylesheet">
<style type="text/css">
.panel.details_panel .panel-heading a { position: relative; top: -5px; }
</style>
@endpush
@push('js')
<script type="text/javascript" src="js/chat_window.js"></script>
<script type="text/javascript" src="js/chat_upload.js"></script>
<script type="text/javascript" src="js/chat_send.js"></script>
<script type="text/javascript" src="js/chat_populate.js"></script>
<script type="text/javascript" src="js/frequent_conv_check.js"></script>
<script type="text/javascript" src="js/enquire.js"></script>
@endpush
@php
function GetCategoryBreadCrumb($Obj){
	$BCAry = [];
	if($Obj) $BCAry[] = $Obj->name;
	if($Obj->parent) array_unshift($BCAry,GetCategoryBreadCrumb($Obj->Parent));
	return implode(" &raquo; ", $BCAry);
}
function Address($Obj){
	$Line1 = implode(", ",[$Obj->address1, $Obj->address2]);
	$Line2 = ($Obj->city)?(implode(', ',[$Obj->City->name, $Obj->City->State->name, $Obj->City->State->Country->name])):'';
	return implode('<br>',[$Line1, $Line2]);
}
function Email($Obj){
	return $Obj->implode('email',',');
}
function Phone($Obj){
	return implode("",['+',$Obj->phonecode,'-',$Obj->phone]);
}
@endphp