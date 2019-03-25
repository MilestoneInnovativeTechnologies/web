@extends("stp.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Customers</strong>{!! PanelHeadButton(Route('stp.customer.new'),'Quick Create New Customer') !!}</div>
		<div class="panel-body">
			<div class="clearfix pagination">
				<div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search by name, email, phone" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchCustomers()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
				<div class="pull-right">{!! $Pagination !!}</div>
			</div>
			<div class="table table-responsive">
				<table class="table table-bordered">
					<thead><tr><th>No</th><th>Name</th><th>Distributor</th><th>Product</th><th>Address</th><th>Action</th></tr></thead>
					@if($Data->isNotEmpty())
					<tbody>
					@foreach($Data as $Line)
						<tr>
							<td>{{ $loop->iteration }}</td>
							<td>{{ $Line->name }}<br><small>({{ $Line->code }})</small></td>
							<td>{{ PartnerParent($Line->Parent1->first()) }}</td>
							<td>{!! ProductDetails($Line->Product) !!}</td>
							<td>{!! PartnerDetails($Line) !!}</td>
							<td>{!! PartnerActions($Line) !!}</td>
						</tr>
					@endforeach
					</tbody>
					@else
					<tbody>
						<tr><td colspan="6"><div class="jumbotron text-center no-record"><h3>No Records found</h3></div></td></tr>
					</tbody>
					@endif
				</table>
			</div>
		</div>
	</div>
</div>

@endsection
@php
function PartnerDetails($P){
	$A = PartnerAddress($P->Details);
	$B = PartnerContact($P);
	return implode("<br>",[$A,$B]);
}
function PartnerAddress($D){
	$S = ', ';
	$Adr = [$D->address1,$D->address2];
	if($D->city) array_push($Adr,'<br>'.$D->City->name,$D->City->State->name,$D->City->State->Country->name);
	return implode($S, $Adr);
}
function PartnerContact($P){
	$No = ['+',$P->Details->phonecode,'-',$P->Details->phone];
	$Emails = $P->Logins->pluck('email')->toArray();
	return ' ' . join('',$No) . '<br/>' . ' ' . join('<br/>', $Emails);
}
function PartnerParent($Parent){
	return $PName = $Parent->ParentDetails->name;
	if(strtolower($Parent->parent) == 'company') return $PName;
	return $Parent->ParentDetails->Parent->ParentDetails->name . '(' .$PName. ')';
}
function PartnerActions($P){
	return implode("",[
		glyLink('javascript:ResetCustomerLogin(\''.$P->code.'\',\''. $P->name .'\',\''. $P->Logins->first()->email .'\')','Send login reset mail to '.$P->name,'log-in',['class'=>'btn btn-none']),
		glyLink('javascript:SendProductInformation(\''.$P->code.'\',\''. $P->name .'\',\''. $P->Logins->first()->email .'\')','Send Product Details and Download links to '.$P->name,'share-alt',['class'=>'btn btn-none']),
		glyLink('javascript:SendProductUpdates(\''.$P->code.'\',\''. $P->name .'\',\''. $P->Logins->first()->email .'\')','Send product\'s latest update details to '.$P->name,'send',['class'=>'btn btn-none']),
		glyLink('javascript:ChangePresaleDates(\''.$P->code.'\',\''. $P->name .'\')','Change presale dates of '.$P->name,'random',['class'=>'btn btn-none']),
		glyLink('javascript:TicketCategoryPermit(\''.$P->code.'\',\''. $P->name .'\')','Allow/Disallow support ticket category for '.$P->name,'filter',['class'=>'btn btn-none']),
		glyLink(Route('stp.customer.edit',['code'=>$P->code]),'Edit details of '.$P->name,'edit',['class'=>'btn btn-none']),
	]);
}
function ProductDetails($Product){
	$Products = $Product->map(function($Prd, $Ind){
		$Nm = $Prd->name;
		$EdnCde = $Prd->pivot->edition;
		$Edns = $Prd->Editions->filter(function($ED, $Ind) use($EdnCde){
			return ($ED->code == $EdnCde);
		})->values();
		$ProductName = $Nm . ' ' . implode("",$Edns->pluck('name')->toArray()) . ' Edition';
		if($Prd->pivot->remarks) $ProductName .= ' (' . $Prd->pivot->remarks . ')';
		$ProductName .= '<br>(' . ($Prd->pivot->installed_on ?: date("Y-m-d",strtotime($Prd->pivot->created_at))) . ')';
		return $ProductName;
	})->toArray();
	return implode("<br>",$Products);
}
function GetCustomerProducts($Data){
	return $Data->mapWithKeys(function($item){
		return [$item->code => $item->Product->mapWithKeys(function($items){
			$EDN = $items->pivot->edition;
			return [$items->code => $items->Editions->map(function($item)use($EDN){
				return ($item->code == $EDN)?$EDN:null;
			})->filter()->values()];
		})];
	});
}
@endphp
@push('css')
<style type="text/css">
	.pagination { margin: 0px !important; }
	.p0 { padding: 0px !important; }
</style>
@endpush
@push('js')
<script type="text/javascript">
$_CustomerProducts = {!! json_encode(GetCustomerProducts($Data)) !!};
$_CustProdSeqs = @php
$Data2 = [];
foreach($Data->groupBy('code')->toArray() as $Code => $Items){
	foreach($Items as $Item){
		$Data2[$Code] = [$Item['name']];
		$Data2[$Code]['Product'] = [];
		foreach($Item['product'] as $Product){
			$Data2[$Code]['Product'][$Product['pivot']['seqno']] = [$Product['pivot']['product'],$Product['name'],$Product['pivot']['edition']];
			foreach($Product['editions'] as $Edition){
				if($Product['pivot']['edition'] == $Edition['code']){
					$Data2[$Code]['Product'][$Product['pivot']['seqno']][3] = $Edition['name'];
					$Data2[$Code]['Product'][$Product['pivot']['seqno']][4] = implode(" ",[$Product['name'],$Edition['name'],'Edition']);
				}
			}
		}
	}
}
echo json_encode($Data2);
@endphp;
</script>
<script type="text/javascript" src="js/stp_customer.js"></script>
<script type="text/javascript" src="js/datepicker.js"></script>
@endpush
@push("css")
<link rel="stylesheet" type="text/css" href="css/datepicker.css">
@endpush