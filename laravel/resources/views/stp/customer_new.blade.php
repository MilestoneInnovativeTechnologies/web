@extends("stp.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default"><form method="post">{{ csrf_field() }}
		<div class="panel-heading"><strong>Add Customer</strong>{!! PanelHeadBackButton(Route('stp.customers'),' Back') !!}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col col-md-6">
					{!! formGroup(2, 'distributor', 'select', 'Distributor', old('distributor',''), ['labelWidth'	=>	4, 'selectOptions'	=>	\App\Models\SupportTeamDistributors::pluck('name','code')->toArray(), 'attr'	=>	'onChange=ChangeDistributor() required']) !!}
					{!! formGroup(2, 'name', 'text', 'Customer Name', old('name',''), ['labelWidth'	=>	4, 'attr'	=>	'required']) !!}
					{!! formGroup(2, 'email', 'text', 'Customer Email', old('email',''), ['labelWidth'	=>	4, 'attr'	=>	'required']) !!}
				</div>
				<div class="col col-md-6">
					{!! formGroup(2, 'country', 'select', 'Customer Country', old('country',''), ['labelWidth'	=>	4, 'selectOptions'	=>	\App\Models\Country::pluck('name','id')->toArray(), 'attr'	=>	'onChange=ChangeCountry() required']) !!}
					{!! formGroup(2, 'phone', 'text', 'Customer Phone', old('phone',''), ['labelWidth'	=>	4, 'inputGroup'	=>	'phonecode', 'attr'	=>	'required']) !!}
					{!! formGroup(2, 'dealer', 'select', 'Dealer if any', old('dealer',''), ['labelWidth'	=>	4]) !!}
				</div>
			</div>
			<hr>
			<div class="row">
				<div class="col col-md-4">{!! formGroup(1, 'product', 'select', 'Product', old('product',''), ['labelWidth'	=>	4, 'attr'	=>	'onChange=ChangeProduct() required']) !!}</div>
				<div class="col col-md-4">{!! formGroup(1, 'edition', 'select', 'Edition', old('edition',''), ['labelWidth'	=>	4, 'attr'	=>	'required']) !!}</div>
				<div class="col col-md-4">{!! formGroup(1, 'presale_enddate', 'text', 'Presale End Date', old('presale_enddate',date('Y-m-d',strtotime('+30 days'))), $extra = ['labelWidth'	=>	4]) !!}</div>
			</div>
		</div>
		<div class="panel-footer clearfix">
			<input type="hidden" name="currency"><input type="hidden" name="phonecode">
			<input type="submit" name="submit" value="Add Customer" class="btn btn-primary pull-right">
		</div>
	</form></div>
</div>

@endsection
@push("css")
<link rel="stylesheet" type="text/css" href="css/datepicker.css">
@endpush
@push('js')
<script type="text/javascript" src="js/stp_customer_new.js"></script>
<script type="text/javascript" src="js/datepicker.js"></script>
<script type="text/javascript">
$(function(){
	$('[name="presale_enddate"]').datepicker({format:'yyyy-mm-dd',autoclose:true,defaultViewDate:'+30d',startDate:'today'})
})
</script>
@endpush
