@extends("action.page")
@section("content")

<?php

if(isset($update) && $update === true){ $update = true; } else { $update = false; }

?>

<div class="content">
	<div class="row">
		<div class="col-sm-2"></div>
		<div class="col-sm-8">
			<form method="post" action="{{ ($update)?(Route('action.update',['id'=>$Item['id']])):(Route('action.store')) }}" class="form {{ ($update)?'update':'create' }}_form">
				{{ ($update) ? method_field('PUT') : '' }}
				{{ csrf_field() }}
				<div class="panel panel-default">
					<div class="panel-heading"><h3>{{ ($update)?('Edit ' . $Item["displayname"]):'Add New Action' }}</h3></div>
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
					
					</div>
					<div class="panel-footer clearfix">
						<div class="pull-right">
							<input class="btn btn-info" type="submit" name="submit" value="{{ ($update)?'Update':'Submit' }}">
							<a href="action" class="btn btn-info">Cancel</a>
						</div>
					</div>
				</div>
			</form>
		</div>
		<div class="col-sm-2"></div>
	</div>
</div>
@endsection