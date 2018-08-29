@extends("tsk.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Contract {{ $MC->code }}</strong>{!! PanelHeadBackButton(url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col col-md-8">
					<div class="table table-responsive">
						<table class="table-striped table">
							<tbody>
								<tr><th width="15%">Customer</th><td>{!! CustomerDetails($MC->Customer) !!}</td></tr>
								<tr><th>Product</th><td>{{ ProductDetails($MC->Registration->toArray(),$MC->registration_seq) }}</td></tr>
								<tr><th>Start Date</th><td>{{ date('d/M/Y',$MC->start_time) }} <small>({{ date('h:i A',$MC->start_time) }})</small> </td></tr>
								<tr><th>End Date</th><td>{{ date('d/M/Y',$MC->end_time) }} <small>({{ date('h:i A',$MC->end_time) }})</small> </td></tr>
								<tr><th>Amount Paid</th><td>{{ round($MC->amount_paid,2) }}</td></tr>
								<tr><th>Actual Amount</th><td>{{ round($MC->amount_actual,2) }}</td></tr>
								<tr><th>Discount</th><td>{!! GetDiscountDetails($MC->amount_actual, $MC->amount_paid) !!}</td></tr>
								<tr><th>Payment Note</th><td>{!! nl2br($MC->payment_note) !!}</td></tr>
								<tr><th>Comments</th><td>{!! nl2br($MC->comments) !!}</td></tr>
								<tr><th>Created On</th><td>{{ date('d/M/Y',strtotime($MC->created_at)) }}</td></tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="col col-md-4">
					<div class="text-center">Current Status</div>
					<div class="text-center well"><h3>{{ $MC->status }}</h3>@if($MC->renewed_to) <small>(Renewed to {{ $MC->renewed_to }})</small> @endif</div>
					{!! GetActions($MC) !!}
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading"><div class="panel-title">Customer Details</div></div>
						<div class="panel-body">
							<div class="table table-responsive">
								<table class="table table-striped">
									<tbody>@php $Cu = $MC->Customer @endphp
										<tr><th>Code</th><td>{{ $Cu->code }}</td></tr>
										<tr><th>Name</th><td>{{ $Cu->name }}</td></tr>
										<tr><th>Phone</th><td>{{ '+' . $Cu->Details->phonecode . '-' . $Cu->Details->phone }}</td></tr>
										<tr><th>Email</th><td>{{ $Cu->Logins->implode('email',', ') }}</td></tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading"><div class="panel-title">Renewed to Contract</div></div>
						<div class="panel-body">
							<div class="table table-responsive">
								<table class="table table-striped">
									<tbody>@if($MC->Renewed && $RN = $MC->Renewed)
										<tr><th>Contract</th><td><a href="{{ Route('mc.view',$RN->code) }}">{{ $RN->code }}</a></td></tr>
										<tr><th>Start Date</th><td>{{ date('d/M/Y',$RN->start_time) }}</td></tr>
										<tr><th>End Date</th><td>{{ date('d/M/Y',$RN->end_time) }}</td></tr>@else
										<tr><th colspan="2"><div class="well">Not Yet Renewed</div></th></tr>@endif
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-9">
					<div class="panel panel-default">
						<div class="panel-heading"><div class="panel-title">Customer Contract History</div></div>
						<div class="panel-body">
							<div class="table table-responsive">
								<table class="table table-bordered">
									<thead><tr><th>Product</th><th>Code</th><th>Contract Sequence</th><th>Start Date</th><th>End Date</th><th>Status</th></tr></thead>
									@php $CH = \App\Models\MaintenanceContract::whereCustomer($MC->customer)->with('Registration')->latest()->take(8)->get()->groupBy('registration_seq')->toArray(); @endphp
									<tbody>@if(!empty($CH))
									@foreach($CH as $seq => $MCA)
									<tr><td rowspan="{{ count($MCA) }}">{{ ProductDetails($MCA[0]['registration'],$seq) }}</td>@foreach($MCA as $mc)
									<td>{{ $mc['code'] }}</td><td>{{ $mc['contract_seq'] }}</td><td>{{ date('d/M/Y',$mc['start_time']) }}</td><td>{{ date('d/M/Y',$mc['end_time']) }}</td><td>{{ $mc['status'] }}</td>
									@if($loop->remaining) </tr><tr> @endif
									@endforeach</tr>
									@endforeach
									@else
									<tr><td colspan="6"><div class="jumbotron text-center"><h3>No more contracts</h3></div></td></tr>
									@endif</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="panel panel-default">
						<div class="panel-heading"><div class="panel-title">Renewed From</div></div>
						<div class="panel-body">
							<div class="table table-responsive">
								<table class="table table-striped">
									<tbody>@if($RF)
										<tr><th>Contract</th><td><a href="{{ Route('mc.view',$RF->code) }}">{{ $RF->code }}</a></td></tr>
										<tr><th>Start Date</th><td>{{ date('d/M/Y',$RF->start_time) }}</td></tr>
										<tr><th>End Date</th><td>{{ date('d/M/Y',$RF->end_time) }}</td></tr>
										<tr><th>Status</th><td>{{ $RF->status }}</td></tr>@else
										<tr><th colspan="2"><div class="well">No Data</div></th></tr>@endif
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
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
	$avps = array_diff($Obj->available_actions,['view']);
	if(empty($avps)) return '';
	if($Obj->renewed_to) $avps = array_diff($avps,['renew','es_mail','et_mail']);
	$TitleIcon = ['view' => ['View this contrat details','list-alt'], 'modify' => ['Modify contract details','edit'], 'delete' => ['Delete this contract','remove'], 'renew' => ['Renew this contract','transfer'],'et_mail' => ['Send mail to customer, notification about the expiry','share-alt'],'es_mail' => ['Send mail to customer, notification about the expiry','share-alt'], 'je_mail' => ['Send Just Expired notification mail to customer','share-alt'],'ex_mail' => ['Send Expired notification mail to customer','share-alt']];
	return implode('',array_map(function($act)use($Obj,$TitleIcon){ 
	 return glyLink('javascript:BrowseUrl(\''.$act.'\',\''.Route('mc.'.$act,$Obj->code).'\',\''.$Obj->code.'\')', $TitleIcon[$act][0], $TitleIcon[$act][1], ['class' => 'btn btn-default vb_'.$act, 'attr' => 'style="width: 100%; margin-bottom: 3px; padding:15px 0px;"', 'text' => ' &nbsp; &nbsp; '.$TitleIcon[$act][0]]);
	},$avps));
}
function GetDiscountDetails($actual, $paid){
	if($actual == 0 || $paid == 0) return 0;
	$amt = $actual - $paid; $per = round(($amt/$actual)*100,2);
	return $amt . ' <small>('.$per.'%)</small>';
}
@endphp
@push('js')
<script type="text/javascript" src="js/mc_mails.js"></script>
@endpush