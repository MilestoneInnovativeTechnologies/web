@extends("distributor.page")
@include('BladeFunctions')
@section("content")
@php
$Deposit = 0;
$from = strtotime(request('from')?:'2000-01-01'); $to = strtotime(request('to')?:'2099-12-31');
 $Transactions = $Distributor->Transactions->filter(function($item,$key)use($from,$to,&$Deposit){
 	$date = strtotime($item->date); $Status = (($date >= $from) && ($date <= $to));
 	if(!$Status && $date < $from) $Deposit += (intval($item->amount) * intval($item->type));
 	return $Status;
 });
@endphp

<div class="content distributor_show">

	<div class="panel panel-default main">
		<div class="panel-heading">
			<strong>{{ $Distributor->name }} - Transactions</strong>
			<div style="display: inline-block; text-align: center; width: 50%; margin-left: 100px; height: 21px;" class="no-print"><form><div class="col-xs-2" style="padding: 0px"><input class="form-control input-sm" name="from" value="{{ request('from') }}"></div><div class="col-xs-2" style="padding: 0px"><input class="form-control input-sm" name="to" value="{{ request('to') }}"></div><div class="col-xs-2 text-left" style="padding: 0px"><button class="btn btn-default btn-sm" type="submit">Submit</button></div></form></div>
			{!! PanelHeadBackButton(url()->previous()) !!}
		</div>
		<div class="panel-body">
		@component('trn.comp_transactions',['Deposit' => $Deposit, 'Transactions' => $Transactions])  @endcomponent
		</div>
	</div>
</div>

@endsection
@push('css')
	<link rel="stylesheet" type="text/css" href="css/datepicker.css">
@endpush
@push('js')
	<script type="text/javascript" src="js/datepicker.js"></script>
	<script>
        $(function(){ $("[name='from'],[name='to']").datepicker({format:'yyyy-mm-dd',autoclose:true,defaultViewDate:'today'});	})
	</script>
@endpush