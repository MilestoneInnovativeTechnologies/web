@extends("tkt.page")
@include('BladeFunctions')
@section("content")
@php $Data = \App\Models\Ticket::whereCode(Request()->tkt)->with(['Customer','Product','Edition','Category','Type','Tasks.Stype','Tasks.Responder.Responder'])->first() @endphp

<div class="content">
	<div class="panel panel-default tkt_panel">
		<div class="panel-heading"><strong>{{ $Data->code }}</strong>{!! PanelHeadButton('javascript:ChangePanelVisibility(\'tkt_panel\')',' ','minus','default') !!}<span class="pull-right"> &nbsp; </span>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('tkt.index',['tkt'=>$Data->code]):url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col col-md-6"><div class="table"><table class="table table-striped tktbasic"><tbody>
					<tr><th class="tkt_title">{{ $Data->title }}</th></tr>
					<tr><td class="tkt_desc">{{ $Data->description }}</td></tr>
				</tbody></table></div></div>
				<div class="col col-md-6"><div class="table"><table class="table table-striped tktextra"><tbody>
					<tr><th>Customer</th><td>{{ $Data->Customer->name }}</td></tr>
					<tr><th>Product</th><td>{{ $Data->Product->name }} {{ $Data->Edition->name }} Edition</td></tr>
					<tr><th>Category</th><td>{!! $Data->category?GetCategoryBreadCrumb($Data->Category):'NONE. <a href="'.Route('tkt.entitle',$Data->code).'">Update</a>' !!}</td></tr>
					<tr><th>Priority</th><td>{!! $Data->priority?:'NONE. <a href="'.Route('tkt.entitle',$Data->code).'">Update</a>' !!}</td></tr>
					<tr><th>Ticket Type</th><td>{!! ($Data->ticket_type)?$Data->Type->name:'NONE. <a href="'.Route('tkt.entitle',$Data->code).'">Update</a>' !!}</td></tr>
					<tr><th>Created At</th><td><script>document.write(ReadableDate('{{ $Data->created_at }}'))</script></td></tr>
				</tbody></table></div></div>
			</div>
		</div>
	</div>
	<div class="panel panel-default tasks_panel">
		<div class="panel-heading"><strong>Tasks</strong>{!! PanelHeadButton('javascript:CreateTask(\''.$Data->code.'\')','Create New Task','plus') !!}</div>
		<div class="panel-body">
			<div class="table table-responsive">
				<table class="table rable-bordered tasks">
					<thead><tr><th nowrap>Task #</th><th width="40%">Details</th><th width="30%">Responder &amp; Handle</th><th width="10%">Weightage</th><th width="15%">Action</th></tr></thead>
					<tbody>@if($Data->Tasks->isNotEmpty())
						@foreach($Data->Tasks as $tsk)
						<tr class="task_row ticket_{{ $Data->code }} task_{{ $tsk->seqno }}">
							<th class="task_no text-center" style="vertical-align:middle">{{ $tsk->seqno }}</th>
							<td class="task_details"><strong>{{ $tsk->title }}</strong> <small>({{ ($tsk->support_type)?$tsk->Stype->name:'' }})</small><br>{!! nl2br($tsk->description) !!}</td>
							<td class="resp_handle"><strong>Responder:</strong><br>{{ ($tsk->Responder)?$tsk->Responder->Responder->name:'' }}<br><br><strong>Handle Method:</strong><br>{!! GetHandleDetails($tsk,$Data->Tasks) !!}</td>
							<td class="weightage"><input type="text" name="weightage[{{ $tsk->id }}]" class="form-control" onkeyup="WeightageChanged('{{ $tsk->seqno }}')" onchange="WeightageChanged('{{ $tsk->seqno }}')" data-wno="{{ $tsk->seqno }}" value="{{ $tsk->weightage }}"></td>
							<td class="action">&nbsp;</td>
						</tr>
						@endforeach
					@endif</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection
@push('js')
<script type="text/javascript">var _TICKET = '{{ $Data->code }}'; </script>
<script type="text/javascript" src="js/tkt_task_manage.js"></script>
@endpush
@php
function GetCategoryBreadCrumb($Obj){
	$BCAry = [];
	if($Obj) $BCAry[] = $Obj->name;
	if($Obj->parent) array_unshift($BCAry,GetCategoryBreadCrumb($Obj->Parent));
	return implode(" &raquo; ", $BCAry);
}
function GetHandleDetails($tsk, $tsks){
	if($tsk->handle_after){
		$html = 'After Tasks<br>';
		foreach($tsk->handle_after->pluck('seqno') as $seqno){
			$html .= '<label class="checkbox-inline" style="margin-left:0px; margin-right:20px; padding-left:0px;">TASK #'.$seqno.'</label>';
		}
		return $html;
	} else {
		return 'Immediate';
	}
}
@endphp
@push('css')
<style type="text/css">
	td.action a:nth-child(n+2){ margin-left: 3px !important; }
</style>
@endpush