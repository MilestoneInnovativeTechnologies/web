@extends("customer.page")
@section("content")

<div class="content customer_show">
	
	<form method="post" action="{{ Route('customer.presale',['customer'=>$Data->code]) }}">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Change presale details of {{ $Data->name }}</strong><a href="{{ Route('customer.index') }}" class="btn btn-default btn-sm pull-right"><span class="glyphicon glyphicon-arrow-left"></span> &nbsp; Back</a></div>
		<div class="panel-body">
			<table class="table table-responsive table-striped">
				<thead><tr><th>No</th><th>Product</th><th>Edition</th><th>Added On</th><th>Registered On</th><th>Presale End Date</th><th>Presale Extend to</th></tr></thead>
				@php $IDs = []; @endphp
				<tbody>@foreach($Data->register as $RObj)
				<tr>
					<td>{{ $loop->iteration }}</td>
					<td>{{ $RObj->Product->name }}</td>
					<td>{{ $RObj->Edition->name }}</td>
					<td>{{ date("d/M/Y",strtotime($RObj->created_at)) }}</td>
					<td>{{ ($RObj->registered_on)?date("d/M/Y",strtotime($RObj->registered_on)):"X" }}</td>
					<td><input class="form-control" type="text" id="e_{{ $RObj->customer }}_{{ $RObj->seqno }}"@php $IDs[] = 'e_'.($RObj->customer).'_'.($RObj->seqno) @endphp name="E[{{ $RObj->seqno }}]" value="{{ ($RObj->presale_enddate)?date('d-m-Y',strtotime($RObj->presale_enddate)):'' }}"></td>
					<td><input class="form-control" type="text" id="x_{{ $RObj->customer }}_{{ $RObj->seqno }}"@php $IDs[] = 'x_'.($RObj->customer).'_'.($RObj->seqno) @endphp name="X[{{ $RObj->seqno }}]" value="{{ ($RObj->presale_extended_to)?date('d-m-Y',strtotime($RObj->presale_extended_to)):'' }}"></td>
				</tr>
				@endforeach</tbody>
			</table>
		</div>
		<div class="panel-footer clearfix">
			<div class="pull-right">
				<input type="submit" name="submit" value="Update" class="btn btn-info">
			</div>
		</div>
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
		$("#{{ implode(',#',$IDs) }}").datepicker({format:'dd-mm-yyyy',autoclose:true,defaultViewDate:'today'});
	})
</script>
@endpush