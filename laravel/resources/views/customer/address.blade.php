@extends("customer.page")
@section("content")

<div class="content change_address">
	<form method="post" action="{{ Route('customer.address') }}" class="form-horizontal">
		<div class="col col-md-2"></div>
		<div class="col col-md-8">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Modify Name and Address</strong><a href="{{ Route('customer.dashboard') }}" class="btn btn-default btn-sm pull-right"><span class="glyphicon glyphicon-arrow-left"></span> Back</a></div>
				<div class="panel-body">
								{{ csrf_field() }}
					<div class="form-group clearfix">
						<div class="col-xs-6">
							<label class="">Name</label>
							<input type="text" name="name" class="form-control" value="{{ old('name',$Partner->name) }}">
						</div>
						<div class="col-xs-6">
							<label class="">Email</label>
							<input type="text" name="email" class="form-control" value="{{ old('email',$Partner->Logins->first()->email) }}">
						</div>
					</div>
					<div class="form-group clearfix">
						<div class="col-xs-6">
							<label class="">Address Line 1</label>
							<input type="text" name="address1" class="form-control" value="{{ old('address1',$Partner->Details->address1) }}">
						</div>
						<div class="col-xs-6">
							<label class="">Address Line 2</label>
							<input type="text" name="address2" class="form-control" value="{{ old('address2',$Partner->Details->address2) }}">
						</div>
					</div>@if($Partner->Details->city)
					<input type="hidden" name="phonecode" value="{{ old('phonecode',$Partner->Details->City->State->Country->phonecode) }}">
					<input type="hidden" name="currency" value="{{ old('currency',$Partner->Details->City->State->Country->currency) }}">
					<div class="form-group clearfix">
						<div class="col-xs-6">
							<label class="">State</label>
							<select name="state" class="form-control" onChange="StateChanged()" data-pre-value="{{ old('state',$Partner->Details->state) }}">
								@foreach($States as $StateObj)
								<option value="{{ $StateObj->id }}"{{ ($StateObj->id == $Partner->Details->state)?' selected':'' }}>{{ $StateObj->name }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-xs-6">
							<label class="">City</label>
							<select name="city" class="form-control" data-pre-value="{{ old('city',$Partner->Details->city) }}">
								@foreach($Cities as $CityObj)
								<option value="{{ $CityObj->id }}"{{ ($CityObj->id == $Partner->Details->city)?' selected':'' }}>{{ $CityObj->name }}</option>
								@endforeach
							</select>
						</div>
					</div>@else
					<div class="form-group clearfix">
						<div class="col-xs-4">
							<label class="">Country</label>
							<select name="country" class="form-control" onChange="CountryChanged()">
							</select>
						</div>
						<input type="hidden" name="phonecode" value="{{ old('phonecode',$Partner->Details->phonecode) }}">
						<input type="hidden" name="currency" value="{{ old('currency',$Partner->Details->currency) }}">
						<div class="col-xs-4">
							<label class="">State</label>
							<select name="state" class="form-control" onChange="StateChanged()">
							</select>
						</div>
						<div class="col-xs-4">
							<label class="">City</label>
							<select name="city" class="form-control">
							</select>
						</div>
					</div>
					@endif
					<div class="form-group clearfix">
						<div class="col-xs-4">
							<label class="">Industry</label>
							<select name="industry" class="form-control">
								<option value=""></option>
								@foreach($Industries as $IndObj)
								<option value="{{ $IndObj->code }}"{{ ($IndObj->code == $Partner->Details->industry)?' selected':'' }}>{{ $IndObj->name }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-xs-4">
							<label class="">Phone</label>
							<div class="input-group">
								<span class="input-group-addon">{{ ($Partner->Details->city) ? $Partner->Details->City->State->Country->phonecode : $Partner->Details->phonecode }}</span>
								<input type="text" name="phone" class="form-control" id="phone" value="{{ old('phone',$Partner->Details->phone) }}">
							</div>
						</div>
						<div class="col-xs-4">
							<label class="">Website</label>
							<input type="text" name="website" class="form-control" value="{{ old('website',$Partner->Details->website) }}">
						</div>
					</div>
				</div>
				<div class="panel-footer clearfix">
					<div class="pull-right clearfix">
						<input type="submit" name="submit" value="Change Address" class="btn btn-primary">
					</div>
				</div>
			</div>
		</div>
		<div class="col col-md-2"></div>
	</form>
	
</div>

@endsection
@push("js")
<script type="text/javascript" src="js/customer_page.js"></script>
@endpush