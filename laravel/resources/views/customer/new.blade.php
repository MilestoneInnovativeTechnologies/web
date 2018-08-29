@extends("customer.page")
@section("content")

<div class="content">
	<form class="form" method="post" action="{{ Route('customer.new') }}" id="new_customer_form">
		{{ csrf_field() }}
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Add New Customer</strong><a href="{{ url()->previous() }}" class="btn btn-default pull-right btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Back</a></div>
			<div class="panel-body">
				<div class="row clearfix">
					<div class="col col-md-6 clearfix" style="border-right: 1px solid #DDD;">
						<div class="form-group clearfix">
							<div class="col col-xs-4">
								<label for="name">Customer Code:</label>
								<input type="text" name="code" id="code" value="{{ old('code')?:$Code }}" class="form-control" required>
							</div>
							<div class="col col-xs-8">
								<label for="name">Name:</label>
								<input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control" required>
							</div>
						</div>
						<div class="form-group clearfix">
							<div class="col col-xs-6">
								<label for="country">Country</label>
								<select name="country" class="form-control" id="country" data-pre-value="{{ old('country') }}">
									<option value=""></option>
								</select>
							</div>
							<div class="col col-xs-6">
								<label for="industry">Industry</label>
								<select name="industry" class="form-control" id="industry" data-pre-value="{{ old('industry') }}">
								</select>
								<div class="new_industry_div" style="display: none;">
									<input type="text" class="form-control reduce_width" id="new_industry" name="new_industry" placeholder="New Industry Name" value="{{ old('new_industry') }}">
									<a href="javascript:NoNewIndustry()" title="Remove"><span class="glyphicon glyphicon-remove pull-right top_adjust"></span></a>
									<div class="clear"></div>
								</div>
							</div>
						</div>
						<div class="form-group clearfix">
							<div class="col col-xs-7">
								<label for="email">Email</label>
								<input type="text" name="email" class="form-control" id="email" required value="{{ old('email') }}">
							</div>
							<div class="col col-xs-5">
								<label for="phone">Phone</label>
								<div class="input-group">
									<span class="input-group-addon phonecode"></span>
									<input type="hidden" name="phonecode" id="phonecode" value="">
									<input type="text" name="phone" class="form-control" id="phone" value="{{ old('phone') }}">
								</div>
							</div>
						</div>
					</div>
					<div class="col col-md-6 clearfix">
						<div class="form-group col col-xs-12">
							<label for="addr1">Address Line 1</label>
							<input type="text" name="address1" class="form-control" id="addr1" value="{{ old('address1') }}">
						</div>
						<div class="form-group clearfix">
							<div class="col col-xs-8">
								<label for="addr2">Address Line 2</label>
								<input type="text" name="address2" class="form-control" id="addr2" value="{{ old('address2') }}">
							</div>
							<div class="col col-xs-4">
								<label for="website">Website</label>
								<input type="text" name="website" class="form-control" id="website" value="{{ old('website') }}">
							</div>
						</div>
						<div class="form-group clearfix">
							<div class="col col-xs-4">
								<label for="state">State</label>
								<select name="state" class="form-control" id="state" data-pre-value="{{ old('state') }}">
									<option value=""></option>
								</select>
							</div>
							<div class="col col-xs-4">
								<label for="city">City</label>
								<select name="city" class="form-control" id="city" data-pre-value="{{ old('city') }}">
									<option value=""></option>
								</select>
							</div>
							<div class="col col-xs-4">
								<label for="currency">Currency</label>
								<input type="text" name="currency" class="form-control" id="currency" value="{{ old('currency') }}">
							</div>
						</div>
					</div>
				</div>
				<div class="row" style="border-top:1px solid #DDD; padding-top: 30px;">
					<div class="form-group clearfix">
						<div class="col col-xs-12">@php $HD = (session("_rolename") == "distributor")?3:4; @endphp
							<div class="col col-xs-{{ $HD }}">
								<label for="product">Product</label>
								<select name="product" class="form-control" id="product" data-pre-value="{{ old('product') }}">
									<option value=""></option>
								</select>
							</div>
							<div class="col col-xs-{{ $HD }}">
								<label for="edition">Edition</label>
								<select name="edition" class="form-control" id="edition" data-pre-value="{{ old('edition') }}">
									<option value=""></option>
								</select>
							</div>
							<div class="col col-xs-{{ $HD }}">
								<label for="presaleend">Presale End Date</label>
								<input type="text" name="presaleend" class="form-control" id="presaleend" value="{{ old('presaleend')?: date("d-m-Y",strtotime('+30 day')) }}">
							</div>@if ($HD == 3)<div class="col col-xs-3">
								<label for="name">Dealer:</label>
								<select name="dealer" class="form-control" id="dealer" data-pre-value="{{ old('dealer') }}">
									<option value=""></option>
								</select>
							</div>@endif
						</div>
					</div>
					<div class="col col-md-6"></div>
				</div>
			</div>
			<div class="panel-footer clearfix">
				<div class="pull-right">
					<input type="submit" name="submit" value="Add New Customer" class="btn btn-info">
				</div>
			</div>
		</div>
	</form>
</div>

@endsection
@push("css")
<link rel="stylesheet" type="text/css" href="css/datepicker.css">
<style type="text/css">
	.reduce_width { width: calc(100% - 20px); float: left !important; }
	.top_adjust { top: 9px; }
</style>
@endpush
@push("js")
<script type="text/javascript" src="js/customer_new.js"></script>
<script type="text/javascript" src="js/datepicker.js"></script>
<script type="text/javascript">
	$(function(){
		$("#presaleend").datepicker({format:'dd-mm-yyyy',autoclose:true,defaultViewDate:'today',startDate:'today'});@if ($HD == 3) LoadDealers(); @endif
	})
</script>
@endpush