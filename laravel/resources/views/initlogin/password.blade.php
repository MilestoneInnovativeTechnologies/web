@extends("layouts.app")
@section('title', 'MIT :: Login')
@include('BladeFunctions')
@section('content')

<div class="content">
	<div class="row">
		<div class="col col-md-8 col-md-offset-2">
			<form method="post">{{ csrf_field() }}
				<div class="panel panel-default">
					<div class="panel-heading"><strong>Create New Password</strong></div>
					<div class="panel-body">
						{!! formGroup('2','password','password','New Password',['attr'=>'required']) !!}
						{!! formGroup('2','password_confirmation','password','Confirm Password',['attr'=>'required']) !!}
					</div>
					<div class="panel-footer clearfix">
						<a href="{{ Route('login') }}" class="btn btn-default"><span class="glyphicon glyphicon-arrow-left"></span> Back to login page</a>
						<input type="submit" name="submit" value="Create Password" class="btn btn-primary pull-right">
					</div>
				</div>
			</form>
		</div>		
	</div>
</div>





@endsection