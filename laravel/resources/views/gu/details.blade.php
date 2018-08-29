@extends("gu.page")
@include('BladeFunctions')
@section("content")
@php $Data = \App\Models\GeneralUpload::find($code)->load(['Customer' => function($Q){ $Q->select('code','name')->with(['Logins' => function($L){ $L->select('id','partner','email'); },'ParentDetails' => function($Q){ $Q->select('code','name')->with(['Logins'=>function($L1){ $L1->select('id','partner','email'); },'Roles'=>function($R1){ $R1->select('code','name'); },'ParentDetails' => function($R){ $R->select('code','name')->with(['Roles'=>function($R2){ $R2->select('code','name'); },'Logins'=>function($L2){ $L2->select('id','partner','email'); }]); }]); }]); }]); @endphp

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>General Form - {{ $Data->code }}</strong>{!! PanelHeadBackButton(Route('gu.index')) !!}</div>
		<div class="panel-body">
			<div class="col-md-5"><div class="table table-responsive"><table class="table table-striped"><tbody>
				<tr><th>Name</th><th>:</th><td>{{ $Data->name }}</td></tr>
				<tr><th>Description</th><th>:</th><td>{{ nl2br($Data->description) }}</td></tr>
				<tr><th>Customer</th><th>:</th><td>{{ ($Data->customer)?$Data->Customer->name:'' }}</td></tr>
				<tr><th>Ticket</th><th>:</th><td>{{ ($Data->ticket)?:'' }}</td></tr>
				<tr><th>File</th><th>:</th><td>{{ ($Data->file)?'Yes':'No' }}</td></tr>
				<tr><th>Date</th><th>:</th><td>{{ ($Data->file)?(date('D d/M/y h:i a',$Data->time)):'' }}</td></tr>
				<tr><th>Overwritable</th><th>:</th><td>{{ (['Y'=>'Yes','N'=>'No'])[$Data->overwrite] }}</td></tr>
			</tbody></table></div></div>
			<div class="col-md-5">
				{!! formGroup(1,'form','textarea','Link to Form',$Data->form,['style' => 'height:120px;']) !!}
				{!! ($Data->file) ? formGroup(1,'download','textarea','Link to Download file',$Data->download,['style' => 'height:120px;']) : '' !!}
			</div>
			<div class="col-md-2">{!! GetActions($Data) !!}</div>
		
		</div>
	</div>
</div>

@endsection
@php
function GetActions($Obj){
	$Ary = [
		'<a href="'.(Route('gu.edit',$Obj->code)).'" class="btn btn-default" style="width: 100%; margin-bottom: 3px; padding:15px 0px;">Edit Form</a>',
		'<a href="javascript:ConfirmDelete(\''.(Route('gu.delete',$Obj->code)).'\')" class="btn btn-default" style="width: 100%; margin-bottom: 3px; padding:15px 0px;">Delete Form</a>',
		'<a href="javascript:SendForm(\''.$Obj->code.'\',\''.$Obj->customer.'\')" class="btn btn-default" style="width: 100%; margin-bottom: 3px; padding:15px 0px;">Send Form</a>',
	];
	if($Obj->file) array_push($Ary,'<a href="'.$Obj->download.'" class="btn btn-default" style="width: 100%; margin-bottom: 3px; padding:15px 0px;">Download file</a>');
	if($Obj->file) array_push($Ary,'<a href="javascript:SendFile(\''.$Obj->code.'\',\''.$Obj->customer.'\')" class="btn btn-default" style="width: 100%; margin-bottom: 3px; padding:15px 0px;">Send file</a>');
	if($Obj->file) array_push($Ary,'<a href="javascript:ConfirmDropFile(\''.Route('gu.drop',$Obj->code).'\')" class="btn btn-default" style="width: 100%; margin-bottom: 3px; padding:15px 0px;">Drop file</a>');
	return implode('',$Ary);
}
@endphp
@push('js')
<script type="text/javascript" src="js/gu_details.js"></script>
@if($Data->customer)
<script type="text/javascript">
var _Customer = ['{{$Data->customer}}','{{$Data->Customer->name}}','{{$Data->Customer->Logins[0]->email}}'];
@php
$Parent = $Data->Customer->ParentDetails[0]; $Dealer = null; $Distributor = null;
if($Parent->Roles->contains('name','dealer'))	{ $Dealer = $Parent; $Distributor = $Dealer->ParentDetails[0]; }
else $Distributor = $Parent;
@endphp
var _Dealer = [@if($Dealer)'{{$Dealer->code}}','{{$Dealer->name}}','{{$Dealer->Logins[0]->email}}'@endif];
var _Distributor = ['{{$Distributor->code}}','{{$Distributor->name}}','{{$Distributor->Logins[0]->email}}'];
</script>
@endif
@endpush