<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Date</th><th>Details</th><th>Amount</th><th>Balance</th><tbody>
	@if($Transactions->isNotEmpty())
	<tr><th>&nbsp;</th><td>&nbsp;</td><th>Opening Balance</th><th colspan="2" class="text-center">{{ $Deposit }}</th></tr>
	@foreach($Transactions as $tr)
	<tr><th>{{ $loop->iteration }}</th><td>{{ date("d/M/Y",strtotime($tr->date)) }}</td><td>{!! nl2br($tr->description) !!}</td><td style="text-align: center">{{ round($tr->amount,2) }}<br><small>({{ $tr->type }})</small></td><td style="text-align: right">{{ $Deposit += ($tr->type*$tr->amount) }}</td></tr>
	@endforeach
	<tr><th>&nbsp;</th><td>&nbsp;</td><th>Closing Balance</th><th colspan="2" class="text-center">{{ $Deposit }}</th></tr>
	@else
	<tr><th colspan="5" style="text-align: center">No records found!</th></tr>
	@endif
</tbody></table></div>