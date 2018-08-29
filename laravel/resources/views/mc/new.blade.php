@extends("tsk.page")
@include('BladeFunctions')
@section("content")
@php $modify = isset($MC); @endphp

<div class="content"><form method="post">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>{{ ($modify)?'Modify' : 'Create' }} Contract</strong>{!! PanelHeadBackButton(url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col col-md-6">
					{!! formGroup(2,'customer','text','Customer',$CR->Customer->name,['labelWidth' => 4, 'attr' => 'readonly']) !!}
					{!! formGroup(2,'registration_seq','text','Product',implode(' ',[$CR->Product->name,$CR->Edition->name,'Edition']),['labelWidth' => 4, 'attr' => 'readonly']) !!}
					<input type="hidden" name="customer" value="{{ $CR->customer }}">
					<input type="hidden" name="registration_seq" value="{{ $CR->seqno }}">@if($modify) <input type="hidden" name="contract_seq" value="{{ $MC->contract_seq }}"><input type="hidden" name="code" value="{{ $MC->code }}"> @endif
					<div class="form-group form-horizontal clearfix">
						<label class="control-label col-xs-4">Start Date</label>
						<div class="col-xs-8"><div class="input-group">
							<input class="form-control start_time_datepicker" type="text" name="start_time" value="{{ date('d-m-Y',($modify)?$MC->start_time:strtotime('now')) }}">
							<span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
						</div></div>
					</div>
					<div class="form-group form-horizontal clearfix">
						<label class="control-label col-xs-4">End Date</label>
						<div class="col-xs-8"><div class="input-group">
							<input class="form-control end_time_datepicker" type="text" name="end_time" value="{{ date("d-m-Y",($modify)?$MC->end_time:strtotime('+1 year')) }}">
							<span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
						</div></div>
					</div>
					{!! formGroup(2,'amount_actual','text','Total Amount',($modify)?$MC->amount_actual:'0.00',['labelWidth' => 4]) !!}
					{!! formGroup(2,'amount_paid','text','Amount Paid',($modify)?round($MC->amount_paid,2):'0.00',['labelWidth' => 4, 'attr' => 'required']) !!}
					{!! formGroup(2,'payment_note','textarea','Payment Note',($modify)?$MC->payment_note:'',['labelWidth' => 4]) !!}
					{!! formGroup(2,'comments','textarea','Comments',($modify)?$MC->comments:'',['labelWidth' => 4]) !!}
				</div>
				<div class="col col-md-6">
					<div class="table table-responsive"><table class="table table-striped"><tbody>
						<tr><th>Distributor</th><td>{{ $DR->name }}</td></tr>
						<tr><th>Price List</th><td>{{ $DR->Pricelist[0]->name }}</td></tr>
						<tr><th>Product Price</th><td>{{ $DR->Pricelist[0]->Details[0]->price }}</td></tr>
						<tr><th>Annual Maintenance Percent</th><td>{{ $MP }}%</td></tr>
						<tr><th>Maintenance Amount</th><td class="mamount">{{ round(($DR->Pricelist[0]->Details[0]->price * $MP)/100,3) }}</td></tr>
					</tbody></table></div>
					<div class="panel panel-default">
						<div class="panel-heading"><div class="panel-title">Previous Contracts</div></div>
						<div class="panel-body"><div class="table table-responsive"><table class="table table-bordered"><thead><tr><th>Code</th><th>Date</th><th>Amount</th><th>Status</th></tr></thead><tbody>@if($CH->isNotEmpty())
							@foreach($CH as $mc)
							@continue($modify && $mc->code == $MC->code)
							<tr><td>{{ $mc->code }}</td><td><small>{!! 'Start Date: ' . date('d/M/Y',$mc->start_time) . '<br>' . 'End Date: ' . date('d/M/Y',$mc->end_time) !!}</small></td><td><small>{!! 'Amount: ' . round($mc->amount_actual,2) . '<br>' . 'Paid: ' . round($mc->amount_paid,2) . '<br>' . 'Note: ' . nl2br($mc->payment_note) !!}</small></td><td><small>{{ $mc->status }}</small></td></tr>
							@endforeach
						@endif</tbody></table></div></div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer clearfix"><input type="submit" name="submit" value="{{ ($modify)?'Update' : 'Create' }} Contract" class="btn btn-primary pull-right"></div>
	</div></form>
</div>

@endsection
@push("css")
<link rel="stylesheet" type="text/css" href="css/datepicker.css">
@endpush
@push("js")
<script type="text/javascript" src="js/datepicker.js"></script>
<script type="text/javascript">
	$(function(){
		$(".start_time_datepicker,.end_time_datepicker").datepicker({format:'dd-mm-yyyy',autoclose:true,defaultViewDate:'today'})
		$('[name="amount_actual"]').val($('td.mamount').text())
	})
</script>
@endpush