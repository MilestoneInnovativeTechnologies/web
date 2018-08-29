@extends("packages.page")
@section("content")

<div class="content">
	<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8"><form method="post" action="packages/{{ $code['code'] }}" onSubmit="return ValidateBaseName()">{{ csrf_field() }}<input type="hidden" name="_method" value="put">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Edit</strong><a href="packages" class="btn btn-default pull-right btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Back</a></div>
				<div class="panel-body">
					<div class="form-group clearfix">
						<div class="col-xs-6">
							<label for="name">Package Code</label>
							<input type="text" class="form-control" id="code" name="code" required value="{{ $code['code'] }}">
						</div>
						<div class="col-xs-6">
							<label for="name">Package Name</label>
							<input type="text" class="form-control" id="name" name="name" required value="{{ $code['name'] }}">
						</div>
					</div>
					<div class="form-group clearfix">
						<div class="col-xs-6">
							<label for="base_name">Base Name (Internal Purpose)</label>
							<input type="text" class="form-control" id="base_name" name="base_name" required value="{{ $code['base_name'] }}">
						</div>
						<div class="col-xs-6">
							<label for="type">Package Type</label>
							<select class="form-control" name="type" id="type">
								<option value="Update"{{ ($code['type']=='Onetime')?'':' selected' }}>Update</option>
								<option value="Onetime"{{ ($code['type']=='Onetime')?' selected':'' }}>Onetime</option>
							</select>
						</div>
					</div>
					<div class="form-group clearfix">
						<div class="col-xs-12">
							<label for="description">Description</label>
							<textarea name="description_public" id="description" class="form-control">{{ $code['description_public'] }}</textarea>
						</div>
					</div>
					<div class="form-group clearfix">
						<div class="col-xs-12">
							<label for="description_internal">Description (for internal purpose)</label>
							<div class="checkbox">
								<label for="same_as_public"><input id="same_as_public" type="checkbox"> Same as Public Description</label>
							</div>
							<textarea name="description_internal" id="description_internal" class="form-control">{{ $code['description_internal'] }}</textarea>
						</div>
					</div>
				</div>
				<div class="panel-footer clearfix">
					<div class="pull-right">
						<button type="submit" class="btn btn-primary">Update</button>
					</div>
				</div>
			</div>
		</form></div>
		<div class="col-md-2"></div>
	</div>
</div>

@endsection