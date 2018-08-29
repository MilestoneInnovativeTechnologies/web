@extends("cpo.page")
@include('BladeFunctions')
@section("content")
@php $Data = \App\Models\CustomerPrintObject::find($code); @endphp
@php $AData = \App\Models\CustomerPrintObject::withoutGlobalScope('active')->where(['customer' => $Data->customer, 'reg_seq' => $Data->reg_seq, 'function_code' => $Data->function_code, 'print_name' => $Data->print_name])->get(); @endphp

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Print Object Details</strong>{!! PanelHeadBackButton((url()->previous() == url()->current()) ? Route('cpo.index') : url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col col-md-5">
					<div class="table table-responsive"><table class="table table-striped"><tbody>
						<tr><th>Code</th><th>:</th><td>{{ $Data->code }}</td></tr>
						<tr><th>Customer</th><th>:</th><td>{{ $Data->Customer->name }}</td></tr>
						<tr><th>Product</th><th>:</th><td>{{ implode(" ",$Data->Product) . ' Edition' }}</td></tr>
						<tr><th>Function Name</th><th>:</th><td>{{ $Data->function_name }}</td></tr>
						<tr><th>Function Code</th><th>:</th><td>{{ $Data->function_code }}</td></tr>
						<tr><th>Print Name</th><th>:</th><td>{{ $Data->print_name }}</td></tr>
						<tr><th>Support User</th><th>:</th><td>{{ $Data->User->name }}</td></tr>
						<tr><th>Date</th><th>:</th><td>{{ date('D d/M/y, h:i a',strtotime($Data->created_at)) }}</td></tr>
					</tbody></table></div>
				</div>
				<div class="col col-md-4">
					<h4>Preview Image</h4>
					@if($Data->preview)<a href="{{ \Storage::disk('printobject')->url($Data->preview) }}" target="_blank"><div style="height: 250px; border: 1px solid #DDD; background-size: contain; background: url('{{ \Storage::disk('printobject')->url($Data->preview) }}') no-repeat top left"></div></a>@else No preview available!! @endif
				</div>
				<div class="col col-md-3">
					{!! GetActions($Data) !!}
				</div>
			</div><hr>
			<div class="row">
				<div class="col col-md-12">
					<h4>Earlier Versions</h4>@if($AData->isNotEmpty())
					<div class="table table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Support User</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead><tbody>
						@foreach($AData as $Obj)
						<tr><th>{{ $loop->iteration }}</th><td>{{ $Obj->User->name }}</td><td>{{ date('D d/M/y h:i a',strtotime($Obj->created_at)) }}</td><td>{{ $Obj->status }}</td><td>{!! GetHistoryActions($Obj) !!}</td></tr>
						@endforeach
					</tbody></table></div>@else
					<div class="jumbotron text-center"><h4>No records found.</h4></div>
				@endif</div>
			</div>
		</div>
	</div>
</div>

@endsection
@php
function GetActions($Obj){
	return implode('',[
		'<a class="btn btn-default" style="width: 100%; margin-bottom: 3px; padding:15px 0px;" target="_blank" href="'.Route('cpo.download',$Obj->code).'">Download</a>',
		'<a class="btn btn-default" style="width: 100%; margin-bottom: 3px; padding:15px 0px;" href="'.Route('cpo.mail',$Obj->code).'">' . ((in_array(session()->get("_rolename"),["customer","dealer","distributor"])) ? 'Get' : 'Send') . ' download link by mail</a>',
	]);
	
}
function GetHistoryActions($Obj){
	$Actions = [glyLink(Route('cpo.download',$Obj->code),'Download','download',['class' => 'btn', 'attr' => 'target="_blank"'])];
	if(!in_array(session()->get('_rolename'),['customer','dealer','distributor'])) $Actions[] = glyLink(Route('cpo.activate',$Obj->code),'Make this Active','flash',['class' => 'btn']);
	glyLink(Route('cpo.mail',$Obj->code),'Download link by mail','envelope',['class' => 'btn']);
	$Actions[] = glyLink(Route('cpo.mail',$Obj->code),((in_array(session()->get('_rolename'),['customer','dealer','distributor'])) ? 'Get' : 'Send') . ' download link by mail','envelope',['class' => 'btn']);
	return implode('',$Actions);
}
@endphp