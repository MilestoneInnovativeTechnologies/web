@extends("tkt.page")
@include('BladeFunctions')
@section("content")
@php
$Tkt = \App\Models\Ticket::whereCode(Request()->tkt)->with(['Customer' => function($Q){ $Q->with('Backups'); },'Product','Edition','Team.Team' => function($Q){ $Q->with('Logins','Details'); }, 'Attachments'])->first();
$Dst = new \App\Models\Distributor();
//dd($Tkt->toArray())
@endphp

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Communicate with {{ $Tkt->Team->Team->name }}</strong>{!! PanelHeadBackButton(Route('tkt.index'),'Back to Tickets') !!}</div>
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
							<div class="col-xs-10 clearfix">
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
					<div class="panel panel-default details_panel ticket">
						<div class="panel-heading">Ticket Details{!! DetailsPanelToggleBtn('ticket') !!}</div>
						<div class="panel-body" style="display: none"><div class="table-responsive"><table class="table table-striped table-condensed"><tbody>
							<tr><th>Code</th><td>{{ $Tkt->code }}</td></tr>
							<tr><th>Title</th><td>{{ $Tkt->title }}</td></tr>
							<tr><th>Product</th><td>{{ $Tkt->Product->name }} {{ $Tkt->Edition->name }} Edition</td></tr>
							<!--<tr><th>Priority</th><td>{{ $Tkt->priority }}</td></tr>-->
							<tr><th>Type</th><td>{{ $Tkt->ticket_type?$Tkt->Type->name:'' }}</td></tr>
							<tr><th>Category</th><td>{{ $Tkt->category?GetCategoryBreadCrumb($Tkt->Category):'' }}</td></tr>
							<tr><th>Descriprion</th><td>{{ $Tkt->description }}</td></tr>
							<tr><th>Created By</th><td>{{ $Tkt->Createdby->name }}, <small>On: {{(date('D d/m h:i A',strtotime($Tkt->created_at)))}}</small></td></tr>@if($Tkt->Attachments->isNotEmpty() && $Aths = $Tkt->Attachments)
							<tr><th colspan="2">&nbsp;</th></tr>
							<tr><th colspan="2">Attachments</th></tr>@foreach($Aths as $ath)
							<tr><th>{{ $ath->name }}</th><td><a href="{{ Route('ticket.download.attachment',$ath->file) }}">Download</a></td></tr>
							@endforeach @endif
						</tbody></table></div></div>
					</div>
					<div class="panel panel-default details_panel backup">
						<div class="panel-heading">Database Backup{!! DetailsPanelToggleBtn('backup') !!}</div>
						<div class="panel-body" style="display: none"><div class="table-responsive"><table class="table table-striped table-condensed"><tbody>@php $B = ($Tkt->Customer->Backups)?:null; @endphp
							@if($B && $B = $B->first())
							<tr><th>Details</th><td>{{ $B->details }}</td></tr>
							<tr><th>Date</th><td>{{ date('d/M/y h:i a',strtotime($B->created_at)) }}</td></tr>
							<tr><th>Size</th><td>{!! GetReadableSize($B->size) !!}</td></tr>
							<tr><td colspan="2"><a href="{{ $B->download_link }}" class="btn btn-default pull-right">Download</a></td></tr>
							@else
							<tr><td colspan="2">No backups</td></tr>
							@endif <form enctype="multipart/form-data" method="post" action="{{ Route('dbb.upload') }}">{{ csrf_field() }}
							<tr><th colspan="2">Upload New</th></tr>
							<tr><th>Details</th><td><textarea name="details" class="form-control"></textarea></td></tr>
							<tr><th>Backup</th><td><input name="backup" class="form-control" type="file"></td></tr>
							<tr><td colspan="2"><input type="submit" value="Upload" class="btn btn-info pull-right"></td></tr></form>
						</tbody></table></div></div>
					</div>
					<div class="panel panel-default details_panel supportteam">
						<div class="panel-heading">Technical Support Team{!! DetailsPanelToggleBtn('supportteam') !!}</div>
						<div class="panel-body" style="display: none"><div class="table-responsive"><table class="table table-striped table-condensed"><tbody>@php $Tm = $Tkt->Team->Team @endphp
							<tr><th>Name</th><td>{{ $Tm->name }}</td></tr>
							<tr><th>Address</th><td>{!! Address($Tm->Details) !!}</td></tr>
							<tr><th>Email</th><td>{{ Email($Tm->Logins) }}</td></tr>
							<tr><th>Phone</th><td>{{ Phone($Tm->Details) }}</td></tr>
						</tbody></table></div></div>
					</div>@if($Dst->count() == 1)
					<div class="panel panel-default details_panel distributor">
						<div class="panel-heading">Distributor Details{!! DetailsPanelToggleBtn('distributor') !!}</div>
						<div class="panel-body" style="display: none"><div class="table-responsive"><table class="table table-striped table-condensed"><tbody>@php $D = $Dst->first() @endphp
							<tr><th>Name</th><td>{{ $D->name }}</td></tr>
							<tr><th>Address</th><td>{!! Address($D->Details) !!}</td></tr>
							<tr><th>Email</th><td>{{ Email($D->Logins) }}</td></tr>
							<tr><th>Phone</th><td>{{ Phone($D->Details) }}</td></tr>
						</tbody></table></div></div>
					</div>
				@endif</div>
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
@endpush
@push('js')
<script type="text/javascript" src="js/chat_window.js"></script>
<script type="text/javascript" src="js/chat_upload.js"></script>
<script type="text/javascript" src="js/chat_send.js"></script>
<script type="text/javascript" src="js/chat_populate.js"></script>
<script type="text/javascript" src="js/frequent_conv_check.js"></script>
<script type="text/javascript" src="js/communicate.js"></script>
<script type="text/javascript">
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
function DetailsPanelToggleBtn($name){
	return PanelHeadButton('javascript:TogglePanelView(\''.$name.'\')','','plus','default','xs');
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