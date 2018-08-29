@extends("distributor.page")
@include("BladeFunctions")
@section("content")
@php
$Distributor = \App\Models\Distributor::whereCode(Request()->code)->with(['Transactions','Customers'  => function($Q){ $Q->with(['ChildDetails','Registration' => function($Q){ $Q->with(['Product','Edition']); }]); },'Dealers','DealerCustomers','Supportteam','Tickets'])->first();
//dd($Distributor->toArray());
@endphp
@php
$Customers = [];
$Distributor->Customers->each(function($item,$key)use(&$Customers){
	if(!$item->ChildDetails) return;
	$Customers[$item->ChildDetails->code] = $item->Registration->map(function($Reg,$key)use($item){ return [$item->ChildDetails->name,$Reg->Product->name,$Reg->Edition->name,date('Y-m-d',strtotime($Reg->created_at)),$Reg->registered_on,null,$Reg->Product->code,$Reg->Edition->code]; })->toArray(); }); $Registrations = $Distributor->Customers->map(function($item){ return $item->Registration;
})->flatten();
$Distributor->DealerCustomers->each(function($item,$key)use(&$Customers){ $item->Children->each(function($itm,$key)use(&$Customers,$item){ $Customers[$itm->ChildDetails->code] = $itm->Registration->map(function($Reg,$key)use($item){ return [$Reg->Customer->name,$Reg->Product->name,$Reg->Edition->name,date('Y-m-d',strtotime($Reg->created_at)),$Reg->registered_on,$item->ChildDetails->name,$Reg->Product->code,$Reg->Edition->code]; })->toArray(); }); });
uasort($Customers,function($A,$B){ return(strtotime($B[0][2])-strtotime($A[0][2])); });
//dd($Customers);
$Dealers = $Distributor->Dealers->map(function($item){ return $item->ChildDetails; });
@endphp

<div class="content">
	<div class="row">
		<div class="col col-md-9">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">{{ $Distributor->name }}</div></div><div class="panel-body">
				<div class="table table-responsive"><table class="table striped"><tbody>
					<tr><th>Code</th><th>:</th><td>{{ $Distributor->code }}</td><th>Phone</th><th>:</th><td>{!! PartnerPhone($Distributor->Details) !!}</td></tr>
					<tr><th>Name</th><th>:</th><td>{{ $Distributor->name }}</td><th>Email</th><th>:</th><td>{!! PartnerEmails($Distributor->Logins) !!}</td></tr>
					<tr><th>Address</th><th>:</th><td>{!! PartnerAddress($Distributor->Details) !!}</td><th>Support Team</th><th>:</th><th>@if($Distributor->Supportteam && $Distributor->Supportteam->isNotEmpty()) <a href="{{ Route('supportteam.panel',$Distributor->Supportteam[0]->Team->code) }}" style="text-decoration: none; color: inherit">{{ $Distributor->Supportteam[0]->Team->name }}</a> @else NONE @endif</th></tr>
				</tbody></table></div>
			</div></div>
		</div>
	</div>
	
<!--
	<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Product Registration Summary</div><select class="pull-right form-control" style="width: 100px; margin-top:-28px; padding:0px;" onChange="PopulateProducts(this.value)" name="prd_reg_period">{!! ProductFilterOptions() !!}</select></div><div class="panel-body">
		<div class="table-responsive"><table class="table table-bordered product_regs"><thead><tr><th>Product</th><th>Edition</th><th>Registered</th><th>Unregistered</th><th>Total</th><tbody>
		</tbody></table></div>
	</div></div>
-->

	<div class="row">
		<div class="col col-md-12">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Product Registration Summary</div><select name="reg_sum_period" class="form-control pull-right"  style="width: 100px; margin-top: -28px; padding: 0px;">{!! GetRegistrationSummaryPeriod() !!}</select></div><div class="panel-body">
			@component('crd.comp_registration_summary',['Data' => $Registrations, 'Distributor' => Request()->code]) @endcomponent
			</div></div>
		</div>

		<div class="col col-md-7"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Transactions</div><a href="{{ Route('mit.transaction.list',Request()->code) }}" class="btn btn-default pull-right btn-sm" style="margin-top: -26px">View Full Transactions</a></div><div class="panel-body">
		@component('trn.comp_transactions',TransAndInitDeposit($Distributor->Transactions)) @endcomponent
		</div></div></div>
		<div class="col col-md-5" style="padding-left: 0px;"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Recent Registrations</div></div><div class="panel-body">
			<div class="table-responsive"><table class="table table-bordered recent_regs"><thead><tr><th>No</th><th>Customer</th><th>Product</th><th>Registered On</th><tbody>
			</tbody></table></div>
		</div></div></div>
	</div>
	
	
	
	<div class="row">
		<div class="col col-md-8"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Customers</div><a href="{{ Route('distributor.customers.list',Request()->code) }}" class="btn btn-default pull-right btn-sm" style="margin-top: -26px">View All Customers</a></div><div class="panel-body">
		@component('crd.comp_customer_reglists',['Customers' => array_slice($Customers,0,10) ]) @endcomponent
		</div></div></div>
		<div class="col col-md-4"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Dealers</div></div><div class="panel-body">
		@component('dealer.comp_dealers',compact('Dealers')) @endcomponent	
		</div></div></div>
	</div>
	<div class="panel panel-default"><div class="panel-heading"><span class="panel-title">Created Tickets</sapn>{!! PanelHeadButton(Route('distributor.created.tickets',Request()->code),' View All Tickets','share-alt','default','xs') !!}</div><div class="panel-body">
	@component('tkt.comp_tickets',['Tickets' => $Distributor->Tickets->chunk(5)->first()]) @endcomponent
	</div></div>
</div>

@endsection
@php
function PartnerAddress($D){
	$Adr = []; $Loc = []; $Obj = [];
	if($D->address1) $Parts[] = $D->address1; if($D->address2) $Parts[] = $D->address2;
	if(implode(', ',$Adr)) $Obj[] = implode(', ',$Adr);
	if($D->city){
		$Loc[] = $D->City->name; $Loc[] = $D->City->State->name;
		if(implode(', ',$Loc)) $Obj[] = implode(', ',$Loc);
		$Obj[] = $D->City->State->Country->name;
	}
	return trim(implode('<br>',$Obj));
}
function PartnerPhone($D){
	return '+' . $D->phonecode . '-' . $D->phone;
}
function PartnerEmails($L){
	return $L->implode('email',', ');
}
function getParentName($P,$R){
	if($P->Roles->contains('name',$R)) return $P->name;
	if($P->ParentDetails->isNotEmpty()) return getParentName($P->ParentDetails[0],$R);
	return null;
}
function TransAndInitDeposit($Trans){
	if(!$Trans || $Trans->isEmpty()) return ['Transactions' => collect([]), 'Deposit' => 0];
	$Max = 10; $Buffer = 30; $Total = $Trans->count();
	if($Max >= $Total || intval(($Max*(100+$Buffer))/100) >= $Total) return ['Transactions' => $Trans, 'Deposit' => 0];
	$chunks = $Trans->chunk(ceil($Max/2)); return ['Transactions' => $chunks->splice(-2)->collapse(), 'Deposit' => GetInitDepositAmount($chunks->collapse())];
}
function GetInitDepositAmount($Trans){
	$Deposit = 0;
	foreach($Trans as $T)
		$Deposit += ($T->type * $T->amount);
	return $Deposit;
}
function ProductFilterOptions(){
	$opts = [strtotime(date('Y-m-d 00:00:00')) => 'Today', strtotime(date('Y-m-d 00:00:00',strtotime('-'.date('w').' days'))) => 'This Week', strtotime(date('Y-m-d 00:00:00',strtotime('-'.(date('w')+7).' days'))) . '&' . strtotime(date('Y-m-d 00:00:00',strtotime('-'.date('w').' days'))) => 'Last Week', strtotime(date('Y-m-01 00:00:00')) => 'This Month', strtotime(date('Y-m-d 00:00:00',strtotime('first day of last month'))) . '&' . strtotime(date('Y-m-01 00:00:00')) => 'Last Month', strtotime(date('Y-'.((intval((date('n')-1)/3)*3)+1).'-1')) => 'This Quarter', strtotime('-'.(((date('n')-1)%3)+3).' month',strtotime(date('Y-m-01'))) . '&' . strtotime(date('Y-'.((intval((date('n')-1)/3)*3)+1).'-1')) => 'Last Quarter', strtotime(date('Y-0'.((intval((date('n')-1)/6)*6)+1).'-01')) => 'This Half Year', strtotime('-'.(((date('n')-1)%6)+6).' month',strtotime(date('Y-m-01'))) . '&' . strtotime(date('Y-0'.((intval((date('n')-1)/6)*6)+1).'-01')) => 'Last Half Year', strtotime(date('Y-01-01')) => 'This Year', strtotime((date('Y')-1).'-01-01') . '&' . strtotime(date('Y-01-01')) => 'Last Year'];
	$optsArray = [];
	array_walk($opts,function($itm, $key)use(&$optsArray){
		$optsArray[] = '<option value="'.$key.'">'.$itm.'</option>';
	});
	return implode('',$optsArray);
}
function GetRegistrationSummaryPeriod(){
	$opts = [strtotime(date('Y-m-d 00:00:00')) => 'Today', strtotime(date('Y-m-d 00:00:00',strtotime('-'.date('w').' days'))) => 'This Week', strtotime(date('Y-m-d 00:00:00',strtotime('-'.(date('w')+7).' days'))) . '&' . strtotime(date('Y-m-d 00:00:00',strtotime('-'.date('w').' days'))) => 'Last Week', strtotime(date('Y-m-01 00:00:00')) => 'This Month', strtotime(date('Y-m-d 00:00:00',strtotime('first day of last month'))) . '&' . strtotime(date('Y-m-01 00:00:00')) => 'Last Month', strtotime(date('Y-'.((intval((date('n')-1)/3)*3)+1).'-1')) => 'This Quarter', strtotime('-'.(((date('n')-1)%3)+3).' month',strtotime(date('Y-m-01'))) . '&' . strtotime(date('Y-'.((intval((date('n')-1)/3)*3)+1).'-1')) => 'Last Quarter', strtotime(date('Y-0'.((intval((date('n')-1)/6)*6)+1).'-01')) => 'This Half Year', strtotime('-'.(((date('n')-1)%6)+6).' month',strtotime(date('Y-m-01'))) . '&' . strtotime(date('Y-0'.((intval((date('n')-1)/6)*6)+1).'-01')) => 'Last Half Year', strtotime(date('Y-01-01')) => 'This Year', strtotime((date('Y')-1).'-01-01') . '&' . strtotime(date('Y-01-01')) => 'Last Year'];
	$optsArray = [];
	array_walk($opts,function($itm, $key)use(&$optsArray){
		$optsArray[] = '<option value="'.($key).'">'.$itm.'</option>';
	});
	return implode('',$optsArray);
}
@endphp
@push('js')
<script type="text/javascript">
function DistributeProducts(J){
	NT = [0,0]; TBD = $('table.product_regs tbody').empty();
	$.each(J,function(P,Ary){
		TR = $('<tr>').appendTo(TBD); TR.append($('<td>').attr('rowspan',Object.keys(Ary).length+1).css({fontWeight:'bold',verticalAlign:'middle',borderBottom:'2px solid #DDD'}).text(P));
		PRD = ''; PT = [0,0]; PC = 0;
		$.each(Ary,function(E,RU){
			if(PC++) TR = $('<tr>').appendTo(TBD); PRD = RU[2];
			TR.append($('<td>').text(E)); R = parseInt(RU[0]); U = parseInt(RU[1]);
			TR.append($('<td>').html(DPDA(R,RU[2],'reg',RU[3])).css({textAlign:'center',verticalAlign:'middle'})); PT[0] += R; NT[0] += R;
			TR.append($('<td>').html(DPDA(U,RU[2],'unreg',RU[3])).css({textAlign:'center',verticalAlign:'middle'})); PT[1] += U; NT[1] += U;
			TR.append($('<td>').html(DPDA(R+U,RU[2],'',RU[3])).css({textAlign:'center',verticalAlign:'middle',fontWeight:'bold'}));
		})
		TR = $('<tr>').appendTo(TBD).css({borderBottom:'2px solid #DDD'}); TR.append($('<th>').text("Total")).append($('<th>').css({textAlign:'center'}).html(DPDA(PT[0],PRD,'reg'))).append($('<th>').css({textAlign:'center'}).html(DPDA(PT[1],PRD,'unreg'))).append($('<th>').css({textAlign:'center',fontWeight:'bold'}).html(DPDA(PT[0]+PT[1],PRD,'')));
	})
	TR = $('<tr>').appendTo(TBD); TR.append($('<th colspan="2">').text("Total")).append($('<th>').css({textAlign:'center',fontWeight:900}).html(DPDA(NT[0],'','reg'))).append($('<th>').css({textAlign:'center',fontWeight:900}).html(DPDA(NT[1],'','unreg'))).append($('<th>').css({textAlign:'center',fontWeight:900}).html(DPDA(NT[0]+NT[1])));
}
function GetFilteredProducts(C,D){
	Products = {}; if(Object.keys(C).length == 0) return Products;
	D = D || 0; D *= 1000;
	$.each(C,function(c,R){
		$.each(R,function(i,r){
			if(typeof Products[r[1]] == 'undefined') Products[r[1]] = {};
			if(typeof Products[r[1]][r[2]] == 'undefined') Products[r[1]][r[2]] = [0,0,r[6],r[7]];
			if((new Date(r[3]).getTime()) >= D){
				if(r[4]) Products[r[1]][r[2]][0]++; else Products[r[1]][r[2]][1]++;
			}
		})
	});
	return Products;
}
function GetRecentRegs(C){
	Customers = []; if(Object.keys(C).length == 0) return Customers;
	$.each(C,function(c,R){
		$.each(R,function(i,r){
			if(r[4] && (new Date(r[4]).getTime() > {{ strtotime(date('Y-m-d 00:00:00',strtotime('-30 days'))) }}*1000)) { r.push(c); Customers.push(r); }
		})
	});
	return Customers.sort(function(c,n){ return ((new Date(n[4]).getTime()) - (new Date(c[4]).getTime())); });
}
function PopulateProducts(T){
	DistributeProducts(GetFilteredProducts(_Customers,T))
}
var _Customers = {!! json_encode($Customers) !!};
function PopulateRecentRegs(){
	TBD = $('.recent_regs tbody').empty();
	$.each(GetRecentRegs(_Customers),function(I,Ary){
		TR = $('<tr>').html([
			$('<td>').text(I+1),
			$('<td>').html(CPA(Ary[8],Ary[0])),
			$('<td>').text([Ary[1],Ary[2],'Edition'].join(" ")),
			$('<td>').text(Ary[4]),
		]).appendTo(TBD);
	})
}
function DistPrdDetailLink(P,T,E){
	Args = ['distributor={{ Request()->code }}','period='+$('[name="prd_reg_period"]').val()];
	if(typeof T != 'undefined' && T != '') Args.push('type='+T);
	if(typeof P != 'undefined' && P != '') Args.push('product='+P);
	if(typeof E != 'undefined' && E != '') Args.push('edition='+E);
	return '{{ Route("mit.reg.detail") }}?'+Args.join('&');
}
function DPDA(C,P,T,E){
	if(!C) return C;
	A = $('<a>').attr({ href:DistPrdDetailLink(P,T,E), target:'_blank' }).css({ textDecoration:'none', color:'inherit' }).text(C)
	return A[0];
}
function CustomerPanelLink(C){
	return '{{ Route("mit.customer.panel","--CODE--") }}'.replace('--CODE--',C);
}
function CPA(C,N){
	if(!C || !N) return N || C;
	A = $('<a>').attr({ href:CustomerPanelLink(C), target:'_blank' }).css({ textDecoration:'none', color:'inherit' }).text(N)
	return A[0];
}
$(function(){
	//PopulateProducts(0);
	PopulateRecentRegs();
})
</script>
@endpush