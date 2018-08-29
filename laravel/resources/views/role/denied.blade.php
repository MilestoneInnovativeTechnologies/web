@extends("role.page")
@section("content")

<div class="content">
	<div class="jumbotron text-center">
		<h2>Access Denied</h2>
		<p><small>You doesn't have the permission to access this resource</small></p>
		<a href="{{ Route('logout') }}" class="btn btn-default">Logout</a>
		<a href="{{ Route('dashboard') }}" class="btn btn-default">Dashboard</a>
	</div>
</div>
	
@endsection