@extends("mc.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Maintenance Contracts - Expiring Soon</strong></div>
		<div class="panel-body">
			<div class="clearfix pagination">
				<div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search for contracts" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchText()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
				<div class="pull-right">{{ $Links }}</div>
			</div>
			<div class="table table-responsive">
				<table class="table table-bordered">
					<thead><tr><th>No</th><th>Contract Code</th><th>Customer</th><th>Product</th><th>Date</th><th>Amount</th><th>Status</th></tr></thead>
					<tbody>
					@if($MCs->isNotEmpty())
					@foreach($MCs as $mc)
					<tr><td>{{ $loop->iteration }}</td><td>{{ $mc->code }}</td><td>{!! CustomerDetails($mc->Customer) !!}</td><td>{!! ProductDetails($mc->Registration->toArray(),$mc->registration_seq) !!}</td><td>{!! DateDetails($mc) !!}</td><td>{!! AmountDetails($mc) !!}</td><td>{{ $mc->status }}@if($mc->renewed_to)<br><small>(Renewed to: {{$mc->renewed_to}})</small> @endif</td></tr>
					@endforeach
					@else
					<tr><td colspan="7"><div class="jumbotron text-center"><h3>No records found</h3></div></td></tr>
					@endif
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection
@php
function CustomerDetails($Cus){
	return $Cus->name . '<br><small>('.$Cus->code.')</small>' ;
}
function ProductDetails($Obj,$seqno){
foreach($Obj as $Reg)
	if($Reg['seqno'] == $seqno)
		return implode(" ",[$Reg['product']['name'],$Reg['edition']['name'],'Edition']);
}
function AmountDetails($Obj){
	return implode("<br>",['Actual Amount: ' . round($Obj->amount_actual,2), 'Paid Amount: ' . round($Obj->amount_paid,2), 'Payment Note: ' .  nl2br($Obj->payment_note)]);
}
function DateDetails($Obj){
	$SD = 'Start Date: ' . date('d/M/Y',$Obj->start_time);
	$ED = 'End Date: ' . date('d/M/Y',$Obj->end_time);
	$Note = '<small>('.floor(($Obj->end_time-time())/86400).' days remaining)</small>';
	return implode("<br>", [$SD,$ED,$Note]);
}

@endphp