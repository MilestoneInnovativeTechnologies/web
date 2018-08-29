@extends("stp.page")
@include('BladeFunctions')
@section("content")

<div class="content change_address">
	<form method="post" action="{{ Route('stp.distributor.edit',['code'=>$Partner->code]) }}" class="form-horizontal">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Modify Details</strong><a href="{{ Route('stp.distributors') }}" class="btn btn-default pull-right btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Back</a></div>
		<div class="panel-body">
				{{ csrf_field() }}
				<div class="col col-md-2"></div>
				<div class="col col-md-8">
					<div class="form-group">
						<div class="col-xs-6">
							<label class="">Name</label>
							<input type="text" name="name" class="form-control" value="{{ $Partner->name }}">
						</div>
						<div class="col-xs-6">
							<label class="">Email</label>
							<input type="text" name="email" class="form-control" value="{{ $Partner->Logins->first()->email }}">
						</div>
					</div>
					<div class="form-group">
						<div class="col-xs-6">
							<label class="">Address Line 1</label>@php $Details = $Partner->Details @endphp
							<input type="text" name="address1" class="form-control" value="{{ $Details->address1 }}">
						</div>
						<div class="col-xs-6">
							<label class="">Address Line 2</label>
							<input type="text" name="address2" class="form-control" value="{{ $Details->address2 }}">
						</div>
					</div>
					<div class="form-group">
						<div class="col-xs-4">
							<label class="">Country</label>
							<select name="country" class="form-control" onChange="CountryChanged(this.value)">
								<option value="">Select Country</option>
								@foreach(\App\Models\Country::all() as $CntObj)
								<option data-currency="{{ $CntObj->currency }}" data-phonecode="{{ $CntObj->phonecode }}" value="{{ $CntObj->id }}"{{ ($Details->city && $CntObj->id == $Details->City->State->country)?' selected':'' }}>{{ $CntObj->name }}</option>
								@endforeach
							</select>
							<input type="hidden" name="phonecode" value="{{ $Details->phonecode }}"><input type="hidden" name="currency" value="{{ $Details->currency }}">
						</div>
						<div class="col-xs-4">
							<label class="">State</label>
							<select name="state" class="form-control" onChange="StateChanged(this.value)">
								@foreach($States as $StateObj)
								<option value="{{ $StateObj->id }}"{{ ($Details->city && $StateObj->id == $Details->City->state)?' selected':'' }}>{{ $StateObj->name }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-xs-4">
							<label class="">City</label>
							<select name="city" class="form-control">
								@foreach($Cities as $CityObj)
								<option value="{{ $CityObj->id }}"{{ ($Details->city && $CityObj->id == $Details->city)?' selected':'' }}>{{ $CityObj->name }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group clearfix">
						<div class="col-xs-6">
							<label class="">Phone</label>
							<div class="input-group">
								<span class="input-group-addon phonecode">{{ $Details->phonecode }}</span>
								<input type="text" name="phone" class="form-control" id="phone" value="{{ $Details->phone }}">
							</div>
						</div>
						<div class="col-xs-6">
							<label class="">Website</label>
							<input type="text" name="website" class="form-control" id="website" value="{{ $Details->website }}">
						</div>
					</div>
				</div>
				<div class="col col-md-2"></div>
		</div>
		<div class="panel-footer clearfix">
			<div class="pull-right clearfix">
				<input type="submit" name="submit" value="Change Address" class="btn btn-primary">
			</div>
		</div>
	</div>
	</form>
	
</div>


@endsection
@push('js')
<script type="text/javascript">
function CountryChanged(C){
	OPT = $('[name="country"] option[value="'+C+'"]');
	CUR = OPT.attr('data-currency'); PCD = OPT.attr('data-phonecode');
	$('[name="currency"]').val(CUR); $('[name="phonecode"]').val(PCD);
	$('.phonecode.input-group-addon').text(PCD);
	LoadStates(C); $('[name="city"]').empty();
}
function LoadStates(C){
	FireAPI('api/'+C+'/states',function(SD){
		$('[name="state"]').html(CreateNodes(SD,'option',1,{value:0})).prepend($('<option>').text('Select State').attr('value','')).val('');
	})
}
function StateChanged(S){
	FireAPI('api/'+S+'/cities',function(CD){
		$('[name="city"]').html(CreateNodes(CD,'option',1,{value:0})).prepend($('<option>').text('Select City').attr('value','')).val('');
	})
}
</script>
@endpush