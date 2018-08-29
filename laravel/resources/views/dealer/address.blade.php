@extends("dealer.page")
@section("content")

<div class="content">
	<form method="post" action="{{ Route('dealer.address') }}" class="form-horizontal">
	<div class="panel panel-default">
		<div class="panel-heading"><h3>Modify Address</h3></div>
		<div class="panel-body">
				{{ csrf_field() }}
				<div class="col col-md-3"></div>
				<div class="col col-md-6">
					<div class="form-group">
						<div class="col-xs-12">
							<label class="">Address Line 1</label>
							<input type="text" name="address1" class="form-control" value="{{ $Details->address1 }}">
						</div>
					</div>
					<div class="form-group">
						<div class="col-xs-6">
							<label class="">Address Line 2</label>
							<input type="text" name="address2" class="form-control" value="{{ $Details->address2 }}">
						</div>
						<div class="col-xs-6">
							<label class="">State</label>
							<select name="state" class="form-control" onChange="StateChanged()">
								@foreach($States as $StateObj)
								<option value="{{ $StateObj->id }}"{{ ($StateObj->id == $Details->state)?' selected':'' }}>{{ $StateObj->name }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group clearfix">
						<div class="col-xs-6">
							<label class="">City</label>
							<select name="city" class="form-control">
								<option value="{{ $City->id }}">{{ $City->name }}</option>
							</select>
						</div>
						<div class="col-xs-6">
							<label class="">Phone</label>
							<div class="input-group">
								<span class="input-group-addon">{{ $Details->phonecode }}</span>
								<input type="text" name="phone" class="form-control" id="phone" value="{{ $Details->phone }}">
							</div>
						</div>
					</div>
				</div>
				<div class="col col-md-3"></div>
		</div>
		<div class="panel-footer clearfix">
			<div class="pull-right clearfix">
				<a href="{{ Route('dashboard') }}" class="btn btn-default">Cancel</a> &nbsp; &nbsp; <input type="submit" name="submit" value="Change Address" class="btn btn-primary">
			</div>
		</div>
	</div>
	</form>
	
</div>

@endsection