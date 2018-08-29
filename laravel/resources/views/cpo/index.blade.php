@extends("cpo.page")
@include('BladeFunctions')
@section("content")
@php $ORM = \App\Models\CustomerPrintObject::with(['Customer' => function($Q){ $Q->select('code','name')->with(['Details','Logins']); }]); @endphp
@php if(Request()->search_text != ""){ $st = '%'.Request()->search_text.'%'; $ORM->where('function_name','like',$st)->orWhere('function_code','like',$st)->orWhereHas('Customer.Logins',function($Q) use($st){ $Q->where('email','like',$st); })->orWhereHas('Customer.Details',function($Q) use($st){ $Q->where('phone','like',$st); })->orWhereHas('Customer',function($Q) use($st){ $Q->where('name','like',$st)->orWhere('code','like',$st); }); } @endphp
@php $Data = $ORM->paginate(10); @endphp

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Customer Print Objects</strong>@unless(in_array(session()->get('_rolename'),['customer','dealer','distributor'])) {!! PanelHeadAddButton(Route('cpo.create'),'Add new Print Object') !!} @endunless</div>
		<div class="panel-body">
			<div class="clearfix pagination">
				<div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchText()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
				<div class="pull-right">{!! $Data->appends(['search_text' => Request()->search_text])->links() !!}</div>
			</div>@if($Data->count())
			<div class="table-responsive">
				<table class="table table-bordered">
					<thead><tr><th>No</th><th>Customer</th><th>Product</th><th>Function</th><th>User</th><th>Action</th></tr></thead>
					<tbody>@foreach($Data as $Obj)
						<tr><th>{{ $loop->iteration }}</th><td>{!! GetCustomerDetails($Obj->Customer) !!}</td><td>{{ implode(' ',$Obj->product).' Edition' }}</td><td>{!! GetFunctionDetails($Obj) !!}</td><td>{!! $Obj->User->name !!}<br><small>{{ date('D d/M/y - h:i A',$Obj->time) }}</small></td><td>{!! GetActions($Obj) !!}</td></tr>
					@endforeach</tbody>
				</table>
			</div>@else
			<div class="jumbotron">
				<h2 class="text-center">No Records found</h2>
			</div>@endif
		</div>
	</div>
</div>

@endsection
@php
function GetCustomerDetails($Cus){
	return $Cus->name . '<br><small>('.$Cus->code.')</small>';
}
function GetFunctionDetails($Obj){
	return $Obj->function_name . ' <small>('.$Obj->function_code.')</small><br><strong>Print Name</strong>: ' . $Obj->print_name;
}
function GetActions($Obj){
	$Actions = [glyLink(Route('cpo.details',$Obj->code),'View details','list-alt',['class' => 'btn'])];
	$Actions[] = glyLink(Route('cpo.preview',$Obj->code),'View/Modify Preview','picture',['class' => 'btn']);
	$Actions[] = glyLink(Route('cpo.download',$Obj->code),'Download','download',['class' => 'btn', 'attr' => 'target="_blank"']);
	$Actions[] = glyLink(Route('cpo.mail',$Obj->code),((in_array(session()->get('_rolename'),['customer','dealer','distributor'])) ? 'Get' : 'Send') . ' download link by mail','envelope',['class' => 'btn']);
	return implode('',$Actions);
}
@endphp