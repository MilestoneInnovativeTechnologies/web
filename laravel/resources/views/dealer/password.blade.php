@extends("dealer.page")
@section("content")

<div class="content">
	<form method="post" action="{{ Route('dealer.password') }}" class="form-horizontal">
	<div class="panel panel-default">
		<div class="panel-heading"><h3>Change Password</h3></div>
		<div class="panel-body">
				{{ csrf_field() }}
				<div class="col col-md-3"></div>
				<div class="col col-md-6">
					<div class="form-group">
						<label class="control-label col-xs-4">Old Password</label>
						<div class="col-xs-8"><input type="password" name="old_password" class="form-control"></div>
					</div>
					<div class="form-group">
						<label class="control-label col-xs-4">New Password</label>
						<div class="col-xs-8"><input type="password" name="password" class="form-control"></div>
					</div>
					<div class="form-group">
						<label class="control-label col-xs-4">Confirm Password</label>
						<div class="col-xs-8"><input type="password" name="password_confirmation" class="form-control"></div>
					</div>
				</div>
				<div class="col col-md-3"></div>
		</div>
		<div class="panel-footer clearfix">
			<div class="pull-right clearfix">
				<a href="{{ Route('dashboard') }}" class="btn btn-default">Cancel</a> &nbsp; &nbsp; <input type="submit" name="submit" value="Change Password" class="btn btn-primary">
			</div>
		</div>
	</div>
	</form>
	
</div>

@endsection