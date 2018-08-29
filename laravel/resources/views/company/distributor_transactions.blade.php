@extends("company.page")
@section("content")

<div class="content distributor_lists">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Transactions of {{ $Partner->name }}</strong><a href="{{ Route('distributor.index') }}" class="btn btn-default pull-right btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Back</a></div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-bordered transactions">
					<thead><tr><th>No</th><th>Code</th><th>Date</th><th>Description</th><th>Price</th><th nowrap>Currency Rate</th><th>Amount (INR)</th><th width="100px">Status</th><th>Action</th></tr></thead>
					<tbody><tr class="primary">
						<td colspan="2" align="center" valign="middle" style="vertical-align: middle">New Transaction</td>
						<td style="vertical-align: middle"><input type="text" name="date" class="form-control" id="datepicker" value="{{ date("Y-m-d") }}"></td>
						<td style="vertical-align: middle"><textarea name="description" class="form-control" style="height: 75px;"></textarea></td>
						<td align="center"><small style="margin-bottom: 3px; display: block;"><select style="border: 1px solid #CCC;" name="type">@foreach(["+","-"] as $type)<option value="{{ $type }}">{{ $type }}</option>@endforeach</select></small><input type="text" class="form-control" name="price" onKeyUp="PriceChanged()"><small>{{ empty(($Data->toArray())[0]) ? "INR" : $Data->first()->currency }}</small></td>
						<td align="center" style="vertical-align: middle"><input type="text" class="form-control" name="exchange_rate" value="1" onKeyUp="ExchangeChanged()"></td>
						<td align="center" style="vertical-align: middle"><input type="text" class="form-control" name="amount" value="" onKeyUp="TotalChanged()"></td>
						<td align="center" style="vertical-align: middle"><select name="status" class="form-control">@foreach(["ACTIVE","INACTIVE","PENDING"] as $status)<option value="{{ $status }}">{{ $status }}</option>@endforeach</select></td>
						<td align="center" style="vertical-align: middle"><a href="javascript:NewTransaction()" class="btn"><span class="glyphicon glyphicon-plus"></span></a></td>
					</tr>@foreach($Data as $Obj)
						<tr data-code="{{ $Obj->code }}" data-status="{{ mb_strtolower($Obj->status) }}">
							<td class="no">{{ $loop->iteration }}</td>
							<td class="code">{{ $Obj->code }}</td>
							<td class="date">{{ date("d/M/Y",strtotime($Obj->date)) }}</td>
							<td class="desc">{{ $Obj->description }}</td>
							<td title="towards {{ ($Obj->type==-1)?'company':'distributor' }}" class="price" nowrap align="center">{{ round($Obj->price,3) }} {{ $Obj->currency }}<br><small>({{ ($Obj->type==-1)?'-':'+' }})</small></td>
							<td class="ex text-center" nowrap style="vertical-align: middle"><input type="text" value="{{ round($Obj->exchange_rate,3) }}" class="form-control" name="ex[{{ $Obj->code }}]" onKeyUp="ExChanged('{{ $Obj->code }}')"></td>
							<td class="amount text-center" nowrap style="vertical-align: middle"><input type="text" value="{{ $Obj->price*$Obj->exchange_rate }}" class="form-control" name="amount[{{ $Obj->code }}]" onKeyUp="AmtChanged('{{ $Obj->code }}')"></td>
							<td class="status text-center" style="vertical-align: middle">{{ $Obj->status }}</td>@php $status = ($Obj->status == "ACTIVE") @endphp
							<td class="action text-center" nowrap style="vertical-align: middle"><a href="javascript:StatusChange('{{ $Obj->code }}')" title="Change Status" class="btn"><span class="glyphicon glyphicon-flash"></span></a><a href="javascript:SubmitChanges('{{ $Obj->code }}')" title="Updates Changes" class="btn"><span class="glyphicon glyphicon-cloud-upload"></span></a></td>
						</tr>
					@endforeach</tbody>
				</table>
			</div>
		</div>
		<div class="panel-footer">
			<div class="row clearfix">
				<div class="col-md-4 text-right"><strong>Summary</strong></div>
				<div class="col-md-2 text-center" title="To Company"><strong>(-)</strong><br><div class="type-minus" style="border-top:1px solid #CCC; font-weight: bold"></div></div>
				<div class="col-md-2 text-center" title="To Distributor"><strong>(+)</strong><br><div class="type-plus" style="border-top:1px solid #CCC; font-weight: bold"></div></div>
				<div class="col-md-2 text-center"><strong>Balance</strong><br><div class="balance" style="border-top:1px solid #CCC; font-weight: bold"></div></div>
				<div class="col-md-2"><form onSubmit="return PrepareTransaction()" method="post" action="{{ Route('mit.newdist.transaction',['distributor'=>$Partner->code]) }}">{{ csrf_field() }}<div class="new_trans_data" style="display: none"></div><input type="submit" name="submit" value="Add Transactions" class="btn btn-primary add_new_trans" disabled></form></div>
			</div>
		</div>
	</div>

</div>

@endsection
@push("css")
<link rel="stylesheet" type="text/css" href="css/datepicker.css">
@endpush
@push("js")
<script type="text/javascript" src="js/datepicker.js"></script>
<script type="text/javascript" src="js/dist_trans.js"></script>
<script type="text/javascript">
var _NewTrans = {};
</script>
@endpush