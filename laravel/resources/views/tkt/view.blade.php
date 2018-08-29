@extends("tkt.page")
@include('BladeFunctions')
@section("content")
@php $Data = \App\Models\Ticket::with('Category','Type','Customer','Product','Edition','Team.Team','Cstatus','Status','Createdby.Roles','Customer.Details','Customer.Logins','Attachments')->whereCode(Request()->tkt)->first() @endphp

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>{{ $Data->code }} - {{ $Data->Cstatus->status }}</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('tkt.index',['tkt'=>$Data->code]):url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col col-md-9">
					<div class="table-responsive">
						<table class="table table-bordered">
							<thead><tr><th>Customer</th><th>Support Team</th><th>Product</th><th>Ticket Created by</th></tr></thead>
							<tbody><tr>
								<td>{{ $Data->Customer->name }}</td><td>{{ $Data->Team->Team->name }}</td><td>{{ $Data->Product->name }} {{ $Data->Edition->name }} Edition</td><td>{{ $Data->Createdby->name }}<br><small>({{ $Data->Createdby->Roles->implode('name',',') }})</small></td>
							</tr></tbody>
						</table>
					</div>
					<div class="table-responsive">
						<table class="table table-striped">
							<tbody>
								<tr><th>Code</th><td>{{ $Data->code }}</td><th>Category</th><td>{{ ($Data->category)?GetCategoryBreadCrumb($Data->Category):'' }}</td></tr>
								<tr><th>Title</th><td>{{ $Data->title }}</td><th>Type</th><td>{{ ($Data->type)?$Data->Type->name:'' }}</td></tr>
								<tr><th>Description</th><td>{{ $Data->description }}</td>@if(session()->get('_rolename') == 'customer')<th>&nbsp;</th><td>&nbsp;</td>@else<th>Priority</th><td>{{ $Data->priority }}</td>@endif</tr>
								<tr><th colspan="4">&nbsp;</th></tr>
								<tr><th>Ticket Created On</th><td><script>document.write(ReadableDate('{{ $Data->created_at }}'))</script></td><th>Ticket Current Status</th><td>{{ $Data->Cstatus->status }}</td></tr>
								<tr><th>Ticket Updated On</th><td><script>document.write(ReadableDate('{{ $Data->updated_at }}'))</script></td><th>Ticket Status Updated On</th><td><script>document.write(ReadableDate('{{ $Data->Cstatus->created_at }}'))</script></td></tr>
							</tbody>
						</table>
					</div>
					<strong>Attachments</strong>
					<div class="table-responsive">
						<table class="table table-striped">
							<tbody>@forelse($Data->Attachments as $ath)
								<tr><td>{{ $loop->iteration }}</td><td>{{ $ath->name }}</td><td><a href="{{ Route('ticket.download.attachment',$ath->file) }}">{{ $ath->file }}</a></td></tr>
							@empty
							<tr><th colspan="4">No attachments found.</th></tr>
							@endforelse</tbody>
						</table>
					</div>
					
				</div>
				<div class="col col-md-3">{!! GetActions($Data) !!}</div>
			</div>
		</div>
	</div>
</div>

@endsection
@php
function GetCategoryBreadCrumb($Obj){
	$BCAry = [];
	if($Obj) $BCAry[] = $Obj->name;
	if($Obj->parent) array_unshift($BCAry,GetCategoryBreadCrumb($Obj->Parent));
	return implode(" &raquo; ", $BCAry);
}
function GetActions($Obj){
	$avps = $Obj->available_actions;
	if(empty($avps)) return '';
	$TitleIcon = ['view' => ['View ticket in detail','list-alt'], 'edit' => ['Edit this ticket','edit'], 'delete' => ['Delete this ticket','remove'], 'entitle' => ['Entitle this ticket','pencil'],'reassign' => ['Assign to another Support Team','user'],'tasks' => ['Manage Tasks','tasks'], 'communicate' => ['Chat with Support Team','comment'],'reopen' => ['Reopen this Ticket','repeat'],'closure' => ['Proceed Ticket closure activities','paperclip'], 'complete' => ['Complete this ticket','ok'],'feedback' => ['Provide feedback about this ticket','check'],'recreate' => ['Recreate same ticket','duplicate'],'enquire' => ['Enquire with customer','headphones'],'close' => ['Close ticket','eye-close'],'req_complete' => ['Send customer, complete request mail','envelope'],'force_complete' => ['Forcibly make this ticket as completed','eject'],'transcript' => ['View chat transcript','italic'],'dismiss' => ['Dismiss ticket with reason','trash']];
	return implode('',array_map(function($act)use($Obj,$TitleIcon){ if($act == 'view') return '';
		return '<a href="'.Route('tkt.'.$act,$Obj->code).'" class="btn btn-default" style="width: 100%; margin-bottom: 3px; padding:15px 0px;">'.$TitleIcon[$act][0].'</a>';
	 return glyLink(Route('tkt.'.$act,$Obj->code), $TitleIcon[$act][0], $TitleIcon[$act][1], ['class' => 'btn btn-link']);
	},$avps));
}
@endphp