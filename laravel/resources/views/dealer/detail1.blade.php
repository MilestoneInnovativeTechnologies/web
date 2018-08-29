@extends("distributor.page")
@section("content")
@php
$Dealer = \App\Models\Dealer::whereCode(Request()->code)->with(['Customers' => function($Q){ $Q->with(['ChildDetails','Registration' => function($Q){ $Q->with(['Product','Edition']); }]); },'Tickets'])->first();
//dd($Dealer->toArray());
@endphp
@php
$Customers = [];
$Dealer->Customers->each(function($item,$key)use(&$Customers){ $Customers[$item->ChildDetails->code] = $item->Registration->map(function($Reg,$key)use($item){ return [$item->ChildDetails->name,$Reg->Product->name,$Reg->Edition->name,date('Y-m-d',strtotime($Reg->created_at)),$Reg->registered_on,'-NA-',$Reg->product,$Reg->edition,$Reg->customer]; })->toArray(); });
uasort($Customers,function($A,$B){ return(strtotime($B[0][2])-strtotime($A[0][2])); });
//dd($Customers)
@endphp

<div class="content">
	<div class="row">
		<div class="col col-md-9">
			<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">{{ $Dealer->name }}</div></div><div class="panel-body">
				<div class="table table-responsive"><table class="table striped"><tbody>
					<tr><th>Code</th><th>:</th><td>{{ $Dealer->code }}</td><th>Phone</th><th>:</th><td>{!! PartnerPhone($Dealer->Details) !!}</td></tr>
					<tr><th>Name</th><th>:</th><td>{{ $Dealer->name }}</td><th>Email</th><th>:</th><td>{!! PartnerEmails($Dealer->Logins) !!}</td></tr>
					<tr><th>Address</th><th>:</th><td>{!! PartnerAddress($Dealer->Details) !!}</td><th>Website</th><th>:</th><td>{{ $Dealer->website }}</td></tr>
					<tr><th>Status</th><th>:</th><td>{{ $Dealer->status }}</td><th>&nbsp;</th><th> </th><td>&nbsp;</td></tr>
				</tbody></table></div>
			</div></div>
		</div>
	</div>
	<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Product Registration Summary</div><select class="pull-right form-control" style="width: 100px; margin-top:-28px; padding:0px;" onChange="PopulateProducts(this.value)" name="prd_reg_period">{!! ProductFilterOptions() !!}</select></div><div class="panel-body">
		<div class="table-responsive"><table class="table table-bordered product_regs"><thead><tr><th>Product</th><th>Edition</th><th>Registered</th><th>Unregistered</th><th>Total</th><tbody>
		</tbody></table></div>
	</div></div>
	<div class="row">
		<div class="col col-md-7"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Customers</div></div><div class="panel-body">
		@component('crd.comp_customer_reglists',['Customers' => array_slice($Customers,0,10) ]) @endcomponent
		</div></div></div>
		<div class="col col-md-5" style="padding-left: 0px;"><div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Recent Registrations</div></div><div class="panel-body">
			<div class="table-responsive"><table class="table table-bordered recent_regs"><thead><tr><th>No</th><th>Customer</th><th>Product</th><th>Registered On</th><tbody>
			</tbody></table></div>
		</div></div></div>
	</div>
	<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Created Tickets</div></div><div class="panel-body">
	@component('tkt.comp_tickets',['Tickets' => $Dealer->Tickets->chunk(5)->first()]) @endcomponent
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
function ProductFilterOptions(){
	$opts = [1 => 'Total',strtotime(date('Y-m-d 00:00:00')) => 'Today', strtotime(date('Y-m-d 00:00:00',strtotime('-1 day'))) => 'From yesterday', strtotime(date('Y-m-d 00:00:00',strtotime('-2 day'))) => 'Last 2 Days', strtotime(date('Y-m-d 00:00:00',strtotime('-6 day'))) => '1 Week', strtotime(date('Y-m-d 00:00:00',strtotime('-29 days'))) => '1 Month', strtotime(date('Y-m-d 00:00:00',strtotime('-179 days'))) => '6 Months', strtotime(date('Y-m-d 00:00:00',strtotime('-364 days'))) => 'Year'];
	$optsArray = [];
	array_walk($opts,function($itm, $key)use(&$optsArray){
		$optsArray[] = '<option value="'.$key.'">'.$itm.'</option>';
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
			if(r[4] && (new Date(r[4]).getTime() > {{ strtotime(date('Y-m-d 00:00:00',strtotime('-30 days'))) }}*1000)) Customers.push(r);
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
	$.each(GetRecentRegs(_Customers),function(I,Ary){ console.log(Ary);
		TR = $('<tr>').html([
			$('<td>').text(I+1),
			$('<td>').html(CPA(Ary[8],Ary[0])),
			$('<td>').text([Ary[1],Ary[2],'Edition'].join(" ")),
			$('<td>').text(Ary[4]),
		]).appendTo(TBD);
	})
}
function DlrPrdDetailLink(P,T,E){
	Args = ['dealer={{ Request()->code }}','period='+$('[name="prd_reg_period"]').val()];
	if(typeof T != 'undefined' && T != '') Args.push('type='+T);
	if(typeof P != 'undefined' && P != '') Args.push('product='+P);
	if(typeof E != 'undefined' && E != '') Args.push('edition='+E);
	return '{{ Route("mit.reg.detail") }}?'+Args.join('&');
}
function DPDA(C,P,T,E){
	if(!C) return C;
	A = $('<a>').attr({ href:DlrPrdDetailLink(P,T,E), target:'_blank' }).css({ color:'inherit' }).text(C)
	return A[0];
}
function CustomerPanelLink(C){
	return '{{ Route("mit.customer.panel","--CODE--") }}'.replace('--CODE--',C);
}
function CPA(C,N){
	if(!C || !N) return N || C;
	A = $('<a>').attr({ href:CustomerPanelLink(C), target:'_blank' }).css({ color:'inherit' }).text(N)
	return A[0];
}
$(function(){
	PopulateProducts(1);
	PopulateRecentRegs();
})
</script>
@endpush