@extends("resource.page")
@section("content")

<?php

if(isset($update) && $update === true){ $update = true; } else { $update = false; }

?>

<div class="content">
	<div class="row">
		<div class="col-sm-2"></div>
		<div class="col-sm-8">
			<form method="post" action="{{ ($update)?(Route('resource.update',['code'=>$Item['code']])):(Route('resource.store')) }}" class="form {{ ($update)?'update':'create' }}_form">
				{{ ($update) ? method_field('PUT') : '' }}
				{{ csrf_field() }}
				<div class="panel panel-default">
					<div class="panel-heading"><h3>{{ ($update)?('Edit ' . $Item["displayname"]):'Add New Resource' }}</h3></div>
					<div class="panel-body">

						<div class="form-group clearfix">
							
							<div class="col-xs-6">
								<label for="displayname" class="control-label">Display Name</label>
								<input class="form-control" type="text" name="displayname" value="{{ ($update)?($Item["displayname"]):(old('displayname')) }}" required>
							</div>
							<div class="col-xs-6">
								<label for="name" class="control-label">Base Name</label>
								<input class="form-control" type="text" name="name" value="{{ ($update)?($Item["name"]):(old('name')) }}" required>
							</div>
						</div>
						<div class="form-group clearfix">
							<div class="col-xs-12">
								<label for="description" class="control-label">Description</label>
								<textarea name="description" class="form-control" rows="8">{{ ($update)?($Item["description"]):(old('description')) }}</textarea>
							</div>
						</div>
						<div class="form-group clearfix">
							<label for="" class="control-label">Resource Actions</label>
								<div class="clearfix">
									@foreach($Actions as $k => $ActObj)
									{!! ($k % 3 === 0)?'</div><div class="clearfix">':'' !!}
									<div class="col-xs-4">
										<label class="switch"><input class="form-control" type="checkbox" id="{{ $ActObj['name'] }}" name="actions[]" value="{{ $ActObj['id'] }}"{{ ($update && is_array($Item['actions']) && in_array($ActObj['id'],$Item['actions']))?' checked':((!$update && is_array(old('actions')) && in_array($ActObj['id'],old('actions')))?' checked':'') }}><div class="slider"></div></label><label class="switch_label" for="{{ $ActObj['name'] }}" title="{{ $ActObj['description'] }}">{{ $ActObj['displayname'] }}</label>
									</div>
									@endforeach
								</div>
						</div>
					
					</div>
					<div class="panel-footer clearfix">
						<div class="pull-right">
							<input class="btn btn-info" type="submit" name="submit" value="{{ ($update)?'Update':'Submit' }}">
							<a href="resource" class="btn btn-info">Cancel</a>
						</div>
					</div>
				</div>
			</form>
		</div>
		<div class="col-sm-2"></div>
	</div>
</div>
@endsection
@push("css")
<style type="text/css">
	.switch_label { position: absolute; margin-left: 20px; }
</style>
@endpush




