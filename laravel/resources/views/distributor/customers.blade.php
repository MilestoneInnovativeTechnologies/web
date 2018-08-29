@extends("distributor.page")
@include('BladeFunctions')
@section("content")
@php
$Customers = [];
$Registrations->groupBy('customer')->each(function($RegArray,$CustomerCode)use(&$Customers){
	$Customers[$CustomerCode] = [];
	foreach($RegArray as $Reg){
		$Array = [$Reg->Customer->name,$Reg->Product->name,$Reg->Edition->name,date('Y-m-d',strtotime($Reg->created_at)),$Reg->registered_on];
		if($Reg->Customer->ParentDetails[0]->Roles->contains('name','dealer')) $Array[] = $Reg->Customer->ParentDetails[0]->name; else $Array[] = null;
		array_push($Array,$Reg->Product->code,$Reg->Edition->code);
		array_push($Customers[$CustomerCode],$Array);
	}
});
//dd($Customers);
@endphp

<div class="content distributor_show">

	<div class="panel panel-default main">
		<div class="panel-heading"><span class="panel-title">Customers</span>{!! PanelHeadBackButton(url()->previous()) !!}</div>
		<div class="panel-body">
		@component('crd.comp_customer_reglists',compact('Customers')) @endcomponent
		</div>
	</div>
</div>

@endsection