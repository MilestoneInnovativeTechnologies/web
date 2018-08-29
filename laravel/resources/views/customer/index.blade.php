@extends("customer.page")
@include('BladeFunctions')
@section("content")
@php
$ORM = new \App\Models\Customer;
$ORM = $ORM->with(['Registration' => function($Q){ $Q->with('Product','Edition'); }]);
//dd($ORM->get()->toArray());
if(Request()->search_text){ $st = '%'.Request()->search_text.'%'; $ORM = $ORM->where(function($Q)use($st){ $Q->where('name','like',$st)->orWhere('code','like',$st)->orWhereHas('Details',function($Q)use($st){ $Q->where('phone','like',$st); })->orWhereHas('Logins',function($Q)use($st){ $Q->where('email','like',$st); }); }); }
$Customers = $ORM->paginate(10);
@endphp

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Customers</strong><a href="{{ Route('customer.new')}}" class="btn btn-info pull-right btn-sm"><span class="glyphicon glyphicon-plus"></span> Create New Customer</a></div>
		<div class="panel-body">
			<div class="clearfix pagination">
				<div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search for customers" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchText()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
				<div class="pull-right">{{ $Customers->appends(['search_text' => Request()->search_text])->links() }}</div>
			</div>
			<div class="table-responsive"><table class="table table-bordered">
				<thead><tr><th>No</th><th>Customer</th><th>Details</th><th>Distributor</th><th>Product</th><th>Actions</th></tr></thead><tbody>
					@forelse($Customers as $Obj)
					@php $Regs = $Obj->Registration; $Cnt = $Regs->count(); @endphp
					<tr>
						<td rowspan="{{ $Cnt }}">{{ $loop->iteration }}</td>
						<td rowspan="{{ $Cnt }}"><small>{{ $Obj->code }}</small><br><strong>{{ $Obj->name }}</strong></td>
						<td rowspan="{{ $Cnt }}">{!! PartnerAddress($Obj->Details) !!}<hr style="margin: 5px 0px">{!! PartnerContact($Obj->Logins,$Obj->Details) !!}</td>
						<td rowspan="{{ $Cnt }}">{{ $Obj->get_distributor()->name }}@if($Obj->get_dealer()) <br><small>Dealer: {{ $Obj->get_dealer()->name }}</small>  @endif</td>
						@foreach($Regs as $reg)
						<td>{{ $reg->Product->name }} {{ $reg->Edition->name }} Edition @if($reg->remarks) ({{ $reg->remarks }}) @endif</td>
						<td>{!! ActionsToListIcons($Obj) !!}{!! ActionsToListIcons2($reg) !!}</td>
						@if($loop->remaining) </tr><tr> @endif
						@endforeach
					</tr>
					@empty
					<tr><th colspan="6" class="text-center">No records found</th></tr>
					@endforelse
				</tbody>
			</table></div>
		</div>
	</div>
</div>

@endsection
@php
function PartnerAddress($Details){
	$Adr = []; $Loc = []; $AdrLines = [];
	if($Details->address1) $Adr[] = $Details->address1; if($Details->address2) $Adr[] = $Details->address2;
	$AdrLines[] = (empty($Adr)) ? "" : implode(", ",$Adr);
	if($Details->city){ $Loc[] = $Details->City->name; $Loc[] = $Details->City->State->name; }
	$AdrLines[] = (empty($Loc)) ? "" : implode(", ",$Loc);
	if($Details->city) $AdrLines[] = $Details->City->State->Country->name;
	return implode('<br>',$AdrLines);
}
function PartnerContact($L,$D){
	$Lines = [];
	if($D->phone) $Lines[] = ('Phone: ').(($D->phonecode)?("+".$D->phonecode."-"):("")).($D->phone);
	if($L && $L->isNotEmpty()){
		$email = $L->implode('email',', ');
		$Lines[] = ('Email: ').($email);
	}
	return implode('<br>',$Lines);
}
function ActionsToListIcons($Obj,$Prop = 'available_actions',$Pref = 'customer',$PK = 'code',$Title = 'action_title',$Icon = 'action_icon',$Modal = 'modal_actions'){
	$LI = [];
	foreach($Obj->$Prop as $act){
		if(in_array($act,$Obj->$Modal)) continue;
		$LI[] = glyLink(Route($Pref.'.'.$act,[$Obj->$PK]),$Obj->$Title[$act],$Obj->$Icon[$act],['class' => 'btn', 'attr' => 'style="padding:6px 6px;"']);
	}
	return implode('',$LI);
}
function ActionsToListIcons2($Obj,$Prop = 'available_actions',$Pref = 'customer',$PK = 'customer',$Title = 'action_title',$Icon = 'action_icon',$Modal = 'modal_actions'){
	$LI = [];
	foreach($Obj->$Prop as $act){
		if(in_array($act,$Obj->$Modal)) continue;
		$LI[] = glyLink(Route($Pref.'.'.$act,[$Obj->$PK,$Obj->seqno]),$Obj->$Title[$act],$Obj->$Icon[$act],['class' => 'btn', 'attr' => 'style="padding:6px 6px;"']);
	}
	return implode('',$LI);
}
@endphp