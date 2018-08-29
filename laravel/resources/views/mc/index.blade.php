@extends("tsk.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>{{ $Title }}</strong></div>
		<div class="panel-body">
			<div class="clearfix pagination">
				<div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search for contracts" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchText()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
				<div class="pull-right">{{ $Links }}</div>
			</div>
			<div class="table table-responsive">
				<table class="table table-bordered">
					<thead><tr><th>No</th><th>Contract Code</th><th>Customer</th><th>Product</th><th>Date</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
					<tbody>
					@if($MCs->isNotEmpty())
					@foreach($MCs as $mc)
					<tr class="c_{{ $mc->code }}"><td>{{ $loop->iteration }}</td><td>{{ $mc->code }}</td><td>{!! CustomerDetails($mc->Customer) !!}</td><td>{!! ProductDetails($mc->Registration->toArray(),$mc->registration_seq) !!}</td><td>{!! DateDetails($mc) !!}</td><td>{!! AmountDetails($mc) !!}</td><td>{{ $mc->status }}@if($mc->renewed_to)<br><small>(Renewed to: {{$mc->renewed_to}})</small> @endif</td><td>{!! GetActions($mc) !!}</td></tr>
					@endforeach
					@else
					<tr><td colspan="8"><div class="jumbotron text-center"><h3>No records found</h3></div></td></tr>
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
	$Days = ($Obj->start_time > time()) ? (floor(($Obj->start_time-time())/86400)) : (floor(($Obj->end_time-time())/86400));
	$Text = ($Obj->start_time > time()) ? ' days to go' : (($Days > 0) ? ' days remaining' : ' days ago');
	$Note = '<small>('.abs($Days).$Text.')</small>';
	return ($Obj->start_time > time()) ? implode("<br>", [$Note,$SD,$ED]) : implode("<br>", [$SD,$ED,$Note]);
}
function GetActions($Obj){
	$avps = $Obj->available_actions;
	if(empty($avps)) return '';
	if($Obj->renewed_to) $avps = array_diff($avps,['renew','es_mail','et_mail']);
	$TitleIcon = ['view' => ['View this contrat details','list-alt'], 'modify' => ['Modify contract details','edit'], 'delete' => ['Delete this contract','remove'], 'renew' => ['Renew this contract','transfer'],'et_mail' => ['Send mail to customer, notification about the expiry','share-alt'],'es_mail' => ['Send mail to customer, notification about the expiry','share-alt'], 'je_mail' => ['Send Just Expired notification mail to customer','share-alt'],'ex_mail' => ['Send Expired notification mail to customer','share-alt']];
	return implode('',array_map(function($act)use($Obj,$TitleIcon){ 
	 return glyLink('javascript:BrowseUrl(\''.$act.'\',\''.Route('mc.'.$act,$Obj->code).'\',\''.$Obj->code.'\')', $TitleIcon[$act][0], $TitleIcon[$act][1], ['class' => 'btn btn-link ib_'.$act]);
	},$avps));
}
@endphp
@push('js')
<script type="text/javascript" src="js/mc_mails.js"></script>
@endpush